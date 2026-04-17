<?php

namespace App\Http\Controllers;

use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SalesController extends Controller
{
    public function index(Request $request)
    {
        // Filters (from query string)
        $from     = $request->query('from');             // YYYY-MM-DD
        $to       = $request->query('to');               // YYYY-MM-DD
        $service  = $request->query('service');          // e.g., "Full Service"
        $pmethod  = $request->query('payment_method');   // cod | gcash
        $customer = $request->query('customer');         // free text: name/email/phone
        $group    = $request->query('group', 'day');     // day | week | month

        // Column-exists helper (works for old/new schemas)
        $has = fn(string $c) => Schema::hasColumn('orders', $c);

        // Base scope: COMPLETED + (PAID if column exists)
        $base = Order::query()
            ->where('status', 'completed')
            ->when($has('payment_status'), fn($q) => $q->where('payment_status', 'paid'))
            ->when($from,    fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,      fn($q) => $q->whereDate('created_at', '<=', $to))
            // service filter adapts: service_type OR service_id via services.title
            ->when($service, function ($q) use ($service, $has) {
                if ($has('service_type')) {
                    $q->where('service_type', $service);
                } elseif ($has('service_id')) {
                    $q->whereIn('service_id', function ($sub) use ($service) {
                        $sub->from('services')->select('id')->where('title', $service);
                    });
                }
            })
            ->when($pmethod, fn($q) => $q->where('payment_method', $pmethod))
            ->when($customer, function ($q) use ($customer) {
                $q->whereHas('user', function ($u) use ($customer) {
                    $u->where('name', 'like', "%{$customer}%")
                        ->orWhere('email', 'like', "%{$customer}%")
                        ->orWhere('phone_number', 'like', "%{$customer}%");
                });
            });

        // Revenue expression that adapts to either schema
        $coalesceTotals = [];
        foreach (['total', 'total_amount'] as $c) {
            if ($has($c)) $coalesceTotals[] = "NULLIF($c,0)";
        }
        if ($coalesceTotals) {
            $revExpr = 'COALESCE(' . implode(', ', $coalesceTotals) . ', 0)';
        } else {
            $addends = [];
            foreach (['subtotal', 'surcharge', 'pickup_delivery_charge', 'total_estimated', 'pud_charge', 'pickup_only_charge'] as $c) {
                if ($has($c)) $addends[] = "COALESCE($c,0)";
            }
            $revExpr = $addends ? implode(' + ', $addends) : '0';
        }

        // KPIs
        $gross = (clone $base)->selectRaw("SUM($revExpr) as agg")->value('agg') ?? 0;
        $count = (clone $base)->count();
        $aov   = $count ? round($gross / $count, 2) : 0;

        // Determine SQL for the chosen grouping (SQLite vs MySQL)
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $expr = match ($group) {
                'week'  => "strftime('%Y-%W', created_at)",     // year-week
                'month' => "strftime('%Y-%m', created_at)",      // year-month
                default => "strftime('%Y-%m-%d', created_at)",   // year-month-day
            };
        } else { // MySQL/MariaDB
            $expr = match ($group) {
                'week'  => "DATE_FORMAT(created_at, '%x-%v')",   // ISO year-week
                'month' => "DATE_FORMAT(created_at, '%Y-%m')",   // year-month
                default => "DATE(created_at)",                   // date
            };
        }

        // Revenue by chosen period (Day/Week/Month)
        $byPeriod = (clone $base)
            ->selectRaw("$expr as bucket, SUM($revExpr) as rev, COUNT(*) as c")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        // Breakdowns — service (adapts to service_id) and payment method
        if ($has('service_type')) {
            $byService = (clone $base)
                ->selectRaw("service_type, SUM($revExpr) as rev, COUNT(*) as c")
                ->groupBy('service_type')
                ->orderByDesc('rev')
                ->get();
        } elseif ($has('service_id')) {
            $byService = (clone $base)
                ->leftJoin('services', 'orders.service_id', '=', 'services.id')
                ->selectRaw("COALESCE(services.title, '(Unknown)') as service_type, SUM($revExpr) as rev, COUNT(*) as c")
                ->groupBy('service_type')
                ->orderByDesc('rev')
                ->get();
        } else {
            $byService = collect();
        }

        $byPayment = (clone $base)
            ->selectRaw("payment_method, SUM($revExpr) as rev, COUNT(*) as c")
            ->groupBy('payment_method')
            ->orderByDesc('rev')
            ->get();

        // Orders table (with users)
        $orders = (clone $base)
            ->with('user')
            ->orderByDesc('created_at')
            ->paginate(15)
            ->withQueryString();

        /* ================== Weekly counts (Mon–Sun) ==================
           Uses created_at of COMPLETED (+PAID if column exists) orders and respects filters.
           For SQLite: strftime('%w') => 0..6 (0=Sun)
           For MySQL:  DAYOFWEEK()    => 1..7 (1=Sun)
        ================================================================= */
        if ($driver === 'sqlite') {
            $weeklyRaw = (clone $base)
                ->selectRaw("CAST(strftime('%w', created_at) AS integer) as dow, COUNT(*) as c")
                ->groupBy('dow')
                ->get()
                ->pluck('c', 'dow')
                ->all();

            // Normalize to Mon..Sun
            $weeklyOrders = [
                $weeklyRaw[1] ?? 0, // Mon
                $weeklyRaw[2] ?? 0, // Tue
                $weeklyRaw[3] ?? 0, // Wed
                $weeklyRaw[4] ?? 0, // Thu
                $weeklyRaw[5] ?? 0, // Fri
                $weeklyRaw[6] ?? 0, // Sat
                $weeklyRaw[0] ?? 0, // Sun
            ];
        } else {
            $weeklyRaw = (clone $base)
                ->selectRaw("DAYOFWEEK(created_at) as dow, COUNT(*) as c")
                ->groupBy('dow')
                ->get()
                ->pluck('c', 'dow')
                ->all();

            // Normalize to Mon..Sun
            $weeklyOrders = [
                $weeklyRaw[2] ?? 0, // Mon
                $weeklyRaw[3] ?? 0, // Tue
                $weeklyRaw[4] ?? 0, // Wed
                $weeklyRaw[5] ?? 0, // Thu
                $weeklyRaw[6] ?? 0, // Fri
                $weeklyRaw[7] ?? 0, // Sat
                $weeklyRaw[1] ?? 0, // Sun
            ];
        }
        /* ============================================================= */

        return view('admin.sales.salesReport', compact(
            'orders',
            'gross',
            'count',
            'aov',
            'byPeriod',
            'byService',
            'byPayment',
            'service',
            'pmethod',
            'customer',
            'from',
            'to',
            'group',
            'weeklyOrders'
        ));
    }

    public function export(Request $request): StreamedResponse
    {
        $from     = $request->query('from');
        $to       = $request->query('to');
        $service  = $request->query('service');
        $pmethod  = $request->query('payment_method');
        $customer = $request->query('customer');

        $has = fn(string $c) => Schema::hasColumn('orders', $c);

        // Same revenue expression as index(), adaptive to schema
        $coalesceTotals = [];
        foreach (['total', 'total_amount'] as $c) {
            if ($has($c)) $coalesceTotals[] = "NULLIF($c,0)";
        }
        if ($coalesceTotals) {
            $revExpr = 'COALESCE(' . implode(', ', $coalesceTotals) . ', 0)';
        } else {
            $addends = [];
            foreach (['subtotal', 'surcharge', 'pickup_delivery_charge', 'total_estimated', 'pud_charge', 'pickup_only_charge'] as $c) {
                if ($has($c)) $addends[] = "COALESCE($c,0)";
            }
            $revExpr = $addends ? implode(' + ', $addends) : '0';
        }

        $rows = Order::query()
            ->where('status', 'completed')
            ->when($has('payment_status'), fn($q) => $q->where('payment_status', 'paid'))
            ->when($from,    fn($q) => $q->whereDate('created_at', '>=', $from))
            ->when($to,      fn($q) => $q->whereDate('created_at', '<=', $to))
            ->when($service, function ($q) use ($service, $has) {
                if ($has('service_type')) {
                    $q->where('service_type', $service);
                } elseif ($has('service_id')) {
                    $q->whereIn('service_id', function ($sub) use ($service) {
                        $sub->from('services')->select('id')->where('title', $service);
                    });
                }
            })
            ->when($pmethod, fn($q) => $q->where('payment_method', $pmethod))
            ->when($customer, function ($q) use ($customer) {
                $q->whereHas('user', function ($u) use ($customer) {
                    $u->where('name', 'like', "%{$customer}%")
                        ->orWhere('email', 'like', "%{$customer}%")
                        ->orWhere('phone_number', 'like', "%{$customer}%");
                });
            })
            ->orderBy('created_at')
            ->get([
                'id',
                'user_id',
                // service column works for both schemas
                $has('service_type')
                    ? 'service_type'
                    : DB::raw("(SELECT title FROM services WHERE services.id = orders.service_id) as service_type"),
                'payment_method',
                DB::raw("$revExpr as total"), // unified Total
                // include these if they exist; else NULLs to keep CSV shape
                $has('subtotal') ? 'subtotal' : DB::raw('NULL as subtotal'),
                $has('surcharge') ? 'surcharge' : DB::raw('NULL as surcharge'),
                $has('pickup_delivery_charge') ? 'pickup_delivery_charge' : DB::raw('NULL as pickup_delivery_charge'),
                'status',
                $has('payment_status') ? 'payment_status' : DB::raw("'paid' as payment_status"),
                'created_at'
            ]);

        $filename = 'sales_export_' . now()->format('Ymd_His') . '.csv';

        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($rows) {
            $out = fopen('php://output', 'w');

            fputcsv($out, [
                'Order ID',
                'User ID',
                'Service',
                'Payment Method',
                'Total',
                'Subtotal',
                'Surcharge',
                'Pickup/Delivery Charge',
                'Status',
                'Payment Status',
                'Created At',
            ]);

            foreach ($rows as $r) {
                fputcsv($out, [
                    $r->id,
                    $r->user_id,
                    $r->service_type,
                    $r->payment_method,
                    $r->total,
                    $r->subtotal,
                    $r->surcharge,
                    $r->pickup_delivery_charge,
                    $r->status,
                    $r->payment_status,
                    optional($r->created_at)->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($out);
        }, 200, $headers);
    }

    // GET /admin/sales/summary (JSON)
    public function summary(Request $request)
    {
        $mode   = $request->query('mode', 'day');  // day | week | month
        $date   = $request->query('date');         // YYYY-MM-DD (for day)
        $start  = $request->query('start');        // YYYY-MM-DD (for week start)
        $year   = (int) $request->query('year');   // e.g., 2025 (for month)
        $month  = (int) $request->query('month');  // 1..12   (for month)

        $has = fn(string $c) => Schema::hasColumn('orders', $c);

        $base = Order::query()
            ->where('status', 'completed')
            ->when($has('payment_status'), fn($q) => $q->where('payment_status', 'paid'));

        // Apply range filter based on mode
        if ($mode === 'day' && $date) {
            $base->whereDate('created_at', $date);
        } elseif ($mode === 'week' && $start) {
            $s = Carbon::parse($start)->startOfDay();
            $e = (clone $s)->addDays(6)->endOfDay();
            $base->whereBetween('created_at', [$s, $e]);
        } elseif ($mode === 'month' && $year && $month) {
            $base->whereYear('created_at', $year)->whereMonth('created_at', $month);
        }

        // Revenue expression (same adaptation)
        $coalesceTotals = [];
        foreach (['total', 'total_amount'] as $c) {
            if ($has($c)) $coalesceTotals[] = "NULLIF($c,0)";
        }
        if ($coalesceTotals) {
            $revExpr = 'COALESCE(' . implode(', ', $coalesceTotals) . ', 0)';
        } else {
            $addends = [];
            foreach (['subtotal', 'surcharge', 'pickup_delivery_charge', 'total_estimated', 'pud_charge', 'pickup_only_charge'] as $c) {
                if ($has($c)) $addends[] = "COALESCE($c,0)";
            }
            $revExpr = $addends ? implode(' + ', $addends) : '0';
        }

        // KPIs
        $gross = (clone $base)->selectRaw("SUM($revExpr) as agg")->value('agg') ?? 0;
        $count = (clone $base)->count();
        $aov   = $count ? round($gross / $count, 2) : 0.0;

        // Grouping by mode
        $driver = DB::connection()->getDriverName();
        if ($driver === 'sqlite') {
            $bucketExpr = match ($mode) {
                'day'   => "strftime('%H', created_at)",         // 00..23
                'week'  => "strftime('%w', created_at)",         // 0..6 (0=Sun)
                default => "strftime('%Y-%m-%d', created_at)",   // month: per-day
            };
        } else {
            $bucketExpr = match ($mode) {
                'day'   => "LPAD(HOUR(created_at), 2, '0')", // 00..23
                'week'  => "DAYOFWEEK(created_at)",          // 1..7 (1=Sun)
                default => "DATE(created_at)",               // month: per-day
            };
        }

        $byPeriodRaw = (clone $base)
            ->selectRaw("$bucketExpr as bucket, SUM($revExpr) as rev, COUNT(*) as c")
            ->groupBy('bucket')
            ->orderBy('bucket')
            ->get();

        // Normalize bucket labels & sort (for week only)
        $byPeriod = $byPeriodRaw->map(function ($r) use ($mode, $driver) {
            $bucket = (string) $r->bucket;
            if ($mode === 'day') {
                $label = sprintf('%s:00', $bucket);
                return ['bucket' => $label, 'c' => (int) $r->c, 'rev' => (float) $r->rev];
            } elseif ($mode === 'week') {
                if ($driver === 'sqlite') { // 0..6 (Sun..Sat)
                    $idx = (int) $bucket;
                    $map = [1=>'Mon',2=>'Tue',3=>'Wed',4=>'Thu',5=>'Fri',6=>'Sat',0=>'Sun'];
                    $order = [1,2,3,4,5,6,0];
                } else { // 1..7 (Sun..Sat)
                    $idx = (int) $bucket;
                    $map = [2=>'Mon',3=>'Tue',4=>'Wed',5=>'Thu',6=>'Fri',7=>'Sat',1=>'Sun'];
                    $order = [2,3,4,5,6,7,1];
                }
                return ['bucket' => $map[$idx] ?? $bucket, 'c' => (int) $r->c, 'rev' => (float) $r->rev, 'wk_idx' => array_search($idx, $order, true)];
            } else {
                return ['bucket' => $bucket, 'c' => (int) $r->c, 'rev' => (float) $r->rev];
            }
        })->when($mode === 'week', fn($c) => $c->sortBy('wk_idx')->values());

        // Top services & all payments (simple view; index() has richer join logic)
        $byService = (clone $base)
            ->selectRaw("CASE WHEN " . ($has('service_type') ? "1=1" : "1=0") . " THEN service_type ELSE '(via service_id)' END as service_type, SUM($revExpr) as rev, COUNT(*) as c")
            ->groupBy('service_type')
            ->orderByDesc('rev')
            ->limit(8)
            ->get()
            ->map(fn($r)=>['service_type'=>$r->service_type,'c'=>(int)$r->c,'rev'=>(float)$r->rev]);

        $byPayment = (clone $base)
            ->selectRaw("payment_method, SUM($revExpr) as rev, COUNT(*) as c")
            ->groupBy('payment_method')
            ->orderByDesc('rev')
            ->get()
            ->map(fn($r)=>['payment_method'=>$r->payment_method,'c'=>(int)$r->c,'rev'=>(float)$r->rev]);

        return response()->json([
            'ok'        => true,
            'kpis'      => ['gross' => (float) $gross, 'count' => (int) $count, 'aov' => (float) $aov],
            'by_period' => $byPeriod,
            'by_service'=> $byService,
            'by_payment'=> $byPayment,
        ]);
    }
}
