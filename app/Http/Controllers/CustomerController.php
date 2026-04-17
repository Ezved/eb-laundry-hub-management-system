<?php
// app/Http/Controllers/CustomerController.php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Customer;
use Illuminate\Support\Facades\DB;
use App\Models\Order;
use App\Models\User;

class CustomerController extends Controller
{
    // MAIN CUSTOMER LIST PAGE
    // MAIN CUSTOMER LIST PAGE
public function index(Request $request)
{
    $search      = trim($request->input('search', ''));
    $show_hidden = $request->boolean('show_hidden', false);
    $sort        = $request->input('sort', 'alpha');  // alpha | recent | most | least

    $customers = Customer::query()
        ->excludeAdmins()
        ->when(!$show_hidden, fn($q) => $q->visible())   // visible = is_hidden false or null
        ->when($show_hidden,  fn($q) => $q->hidden())
        ->when($search !== '', function ($q) use ($search) {
            $q->where(function ($qq) use ($search) {
                $qq->where('name', 'like', "%{$search}%")
                   ->orWhere('email', 'like', "%{$search}%")
                   ->orWhere('phone_number', 'like', "%{$search}%")
                   ->orWhere('address', 'like', "%{$search}%");
            });
        });

    // --- Sorting logic ---
    if (in_array($sort, ['recent', 'most', 'least'], true)) {
        // Add subqueries for order count + last order date
        $customers->addSelect([
            'orders_count' => Order::selectRaw('COUNT(*)')
                ->where(function ($q) {
                    $q->whereColumn('orders.customer_id', 'customers.id')
                      ->orWhere(function ($or) {
                          $or->whereColumn('orders.user_id', 'customers.user_id')
                             ->whereNotNull('customers.user_id');
                      });
                }),
            'last_order_at' => Order::selectRaw('MAX(created_at)')
                ->where(function ($q) {
                    $q->whereColumn('orders.customer_id', 'customers.id')
                      ->orWhere(function ($or) {
                          $or->whereColumn('orders.user_id', 'customers.user_id')
                             ->whereNotNull('customers.user_id');
                      });
                }),
        ]);

        if ($sort === 'recent') {
            // Latest order at the top
            $customers->orderByDesc('last_order_at')->orderBy('name');
        } elseif ($sort === 'most') {
            // Highest order count first
            $customers->orderByDesc('orders_count')->orderBy('name');
        } else { // least
            // Lowest order count first
            $customers->orderBy('orders_count')->orderBy('name');
        }
    } else {
        // Default: alphabetical
        $sort = 'alpha';
        $customers->orderBy('name');
    }

    $customers = $customers->paginate(10)->withQueryString();

    return view('admin.customers.customers', compact('customers', 'show_hidden', 'sort'));
}


    // AJAX SEARCH FOR LIVE SEARCH BOX
public function ajaxSearch(Request $request)
{
    $q           = trim($request->get('q', ''));
    $show_hidden = $request->boolean('show_hidden', false);
    $sort        = $request->input('sort', 'alpha');   // same options as index()

    $customers = Customer::query()
        ->excludeAdmins()
        ->when(!$show_hidden, fn($qq) => $qq->visible())
        ->when($show_hidden,  fn($qq) => $qq->hidden())
        ->when($q !== '', function ($qq) use ($q) {
            $qq->where(function ($w) use ($q) {
                $w->where('name', 'like', "%{$q}%")
                  ->orWhere('email', 'like', "%{$q}%")
                  ->orWhere('phone_number', 'like', "%{$q}%")
                  ->orWhere('address', 'like', "%{$q}%");
            });
        });

    // Same sort logic as index()
    if (in_array($sort, ['recent', 'most', 'least'], true)) {
        $customers->addSelect([
            'orders_count' => Order::selectRaw('COUNT(*)')
                ->where(function ($q2) {
                    $q2->whereColumn('orders.customer_id', 'customers.id')
                       ->orWhere(function ($or) {
                           $or->whereColumn('orders.user_id', 'customers.user_id')
                              ->whereNotNull('customers.user_id');
                       });
                }),
            'last_order_at' => Order::selectRaw('MAX(created_at)')
                ->where(function ($q2) {
                    $q2->whereColumn('orders.customer_id', 'customers.id')
                       ->orWhere(function ($or) {
                           $or->whereColumn('orders.user_id', 'customers.user_id')
                              ->whereNotNull('customers.user_id');
                       });
                }),
        ]);

        if ($sort === 'recent') {
            $customers->orderByDesc('last_order_at')->orderBy('name');
        } elseif ($sort === 'most') {
            $customers->orderByDesc('orders_count')->orderBy('name');
        } else {
            $customers->orderBy('orders_count')->orderBy('name');
        }
    } else {
        $customers->orderBy('name');
    }

    $customers = $customers->get();

    return view('admin.customers.partials.rows', compact('customers', 'show_hidden'));
}


    // HIDE CUSTOMER (works on customers table)
    public function hide(Customer $customer)
    {
        $customer->update(['is_hidden' => true]);

        // Mirror to linked user
        if ($customer->user) {
            $customer->user->update(['is_hidden' => true]);
        }

        return redirect()->route('customers', request()->only('search') + ['show_hidden' => 0])
            ->with('success', 'Customer hidden.');
    }

    public function unhide(Customer $customer)
    {
        $customer->update(['is_hidden' => false]);

        if ($customer->user) {
            $customer->user->update(['is_hidden' => false]);
        }

        $stayHidden = request()->boolean('show_hidden', true);
        return redirect()->route('customers', request()->only('search') + ['show_hidden' => ($stayHidden ? 1 : 0)])
            ->with('success', 'Customer unhidden.');
    }

    public function edit(Customer $customer)
    {
        return view('admin.customers.edit', compact('customer'));
    }

    public function update(Request $request, Customer $customer)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'email'        => ['nullable','email','max:255'],
            'phone_number' => ['nullable','string','max:50'],
            'address'      => ['nullable','string','max:255'],
        ]);

        $customer->update($data);

        return redirect()
            ->route('customers', $request->only('search') + [
                'show_hidden' => $request->boolean('show_hidden', false) ? 1 : 0,
            ])
            ->with('success', 'Customer updated.');
    }

    public function destroy(Customer $customer)
    {
        DB::transaction(function () use ($customer) {
            // 1) Collect all orders tied to this customer or its linked user
            $orderQuery = Order::query()->where('customer_id', $customer->id);

            if ($customer->user_id) {
                $orderQuery->orWhere('user_id', $customer->user_id);
            }

            $orders = $orderQuery->get();

            // 2) Delete any order items first (if relation exists), then the orders
            foreach ($orders as $order) {
                if (method_exists($order, 'items')) {
                    $order->items()->delete();   // order_items table, if you have it
                }
                $order->delete();
            }

            // 3) Delete the linked user account (so it disappears from Users page)
            if ($customer->user_id && $customer->user) {
                $customer->user->delete();
            }

            // 4) Finally delete the customer record itself
            $customer->delete();
        });

        return redirect()
            ->route('customers')
            ->with('success', 'Customer, their account, and all of their orders have been deleted.');
    }

    /**
     * Order history for this customer (works for both Users and Walk-ins).
     */
    public function orders(Customer $customer, Request $request)
{
    // For the header in orders.blade.php:
    $headerUser = $customer->user ?? $customer;

    $query = Order::query()
        ->with(['user', 'customer'])
        ->where(function ($q) use ($customer) {
            // Walk-in + from-customer orders (use customer_id)
            $q->where('customer_id', $customer->id);

            // Also include orders saved with user_id if this Customer is linked to a User
            if ($customer->user_id) {
                $q->orWhere('user_id', $customer->user_id);
            }
        });

    $sort = $request->input('sort', 'desc');   // desc | asc | all
    $date = $request->input('date');           // YYYY-MM-DD

    if ($date) {
        $query->whereDate('created_at', $date);
    }

    if ($sort === 'asc') {
        $query->orderBy('created_at', 'asc');
    } else {
        $query->orderBy('created_at', 'desc');
    }

    $orders = $query->paginate(15)->appends($request->only('sort', 'date'));

    return view('admin.customers.orders', [
        'user'   => $headerUser,
        'orders' => $orders,
    ]);
}

public function loyalty(Customer $customer)
{
    // For the header in loyalty.blade.php
    // If the Customer has a linked User, use that; otherwise use the Customer model.
    $headerUser = $customer->user ?? $customer;

    // Base query: include both walk-in & user-linked orders
    $base = Order::query()
        ->with(['user', 'customer'])
        ->where(function ($q) use ($customer) {
            // Orders explicitly tied to this customer (walk-in + from-customer)
            $q->where('customer_id', $customer->id);

            // If this Customer is linked to a User, also pull orders by user_id
            if ($customer->user_id) {
                $q->orWhere('user_id', $customer->user_id);
            }
        });

    // Completed orders are what count toward free services
    $completedCount = (clone $base)
        ->where('status', 'completed')
        ->count();

    // Simple rule: 10 completed orders = 1 free service
    $earned   = intdiv($completedCount, 10);
    $progress = $completedCount % 10;
    $toNext   = $progress === 0 ? 10 : (10 - $progress);

    // Recent orders to show in the table (both walk-in + pickup/delivery)
    $recent = $base
        ->orderByDesc('created_at')
        ->limit(30)
        ->get();

    return view('admin.customers.loyalty', [
        'user'      => $headerUser,
        'completed' => $completedCount,
        'earned'    => $earned,
        'progress'  => $progress,
        'toNext'    => $toNext,
        'recent'    => $recent,
    ]);
}

}
