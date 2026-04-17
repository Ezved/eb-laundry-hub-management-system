<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Customer;
use App\Models\Order;


class UserController extends Controller
{
    /* ---------- AJAX: Users table rows (admin/users) ---------- */



    public function ajaxSearch(Request $request)
    {
        $search     = $request->input('q', '');
        $showHidden = (bool) $request->boolean('show_hidden', false);

        $users = User::when(!$showHidden, fn($q) => $q->where('is_hidden', false))
            ->when($showHidden,  fn($q) => $q->where('is_hidden', true))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.users.partials.rows', [
            'all_users'   => $users,
            'authId'      => auth()->id(),
            'adminCount'  => User::where('role', 'admin')->where('is_hidden', false)->count(),
            'show_hidden' => $showHidden,
        ]);
    }

    /* ---------- Admin: Customers list ---------- */
    public function loadAllCustomers(Request $request)
    {
        $search = trim($request->get('search', ''));

        $customers = User::where('role', 'user')
            ->when($search !== '', function ($q) use ($search) {
                $q->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%")
                        ->orWhere('address', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('admin.customers.customers', compact('customers'));
    }

    /* ---------- Admin: Users list ---------- */
    public function loadAllUsers(Request $request)
    {
        $search     = $request->input('search', '');
        $showHidden = (bool) $request->boolean('show_hidden', false);

        $users = User::when(!$showHidden, fn($q) => $q->where('is_hidden', false))
            ->when($showHidden,  fn($q) => $q->where('is_hidden', true))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%")
                        ->orWhere('phone_number', 'like', "%{$search}%");
                });
            })
            ->orderBy('name')
            ->get();

        return view('admin.users.users', [
            'all_users'   => $users,
            'show_hidden' => $showHidden,
        ]);
    }

    /* ---------- Admin: Add / Edit ---------- */
    public function loadaddUserForm()
    {
        return view('admin.users.add_user');
    }

    public function addUser(Request $request)
    {
        $request->validate([
            'full_name'    => 'required|string|max:255',
            'email'        => 'required|email|unique:users,email',
            'phone_number' => 'required|string|max:20|unique:users,phone_number',
            'password'     => 'required|string|confirmed|min:8',
            'role'         => 'required|in:admin,user',
        ]);

        try {
            $u = new User();
            $u->name         = $request->full_name;
            $u->email        = $request->email;
            $u->phone_number = $request->phone_number;
            $u->password     = Hash::make($request->password);
            $u->role         = $request->input('role', 'user');
            $u->status       = 'active';
            $u->save();

            return redirect('/users')->with('success', 'User Added Successfully');
        } catch (\Exception $e) {
            return redirect('/add/users')->with('fail', 'Failed to add user: ' . $e->getMessage());
        }
    }

    public function loadEditForm($id)
    {
        $user = User::findOrFail($id);
        return view('admin.users.edit_user', compact('user'));
    }

    public function editUser(Request $request)
    {
        $validated = $request->validate([
            'user_id'      => ['required', 'exists:users,id'],
            'full_name'    => ['required', 'string', 'max:255'],
            'email'        => ['required', 'email', Rule::unique('users', 'email')->ignore($request->user_id)],
            'phone_number' => ['required', 'string', 'max:20', Rule::unique('users', 'phone_number')->ignore($request->user_id)],
            'role'         => ['required', 'in:admin,user'],
            'password'     => ['nullable', 'confirmed', 'min:8'],
        ]);

        $target = User::findOrFail($validated['user_id']);

        // Prevent self-demotion
        if ($target->id === auth()->id() && $validated['role'] !== 'admin') {
            return redirect()->route('users')->with('fail', 'You cannot remove your own admin role.');
        }
        // Prevent demoting the last remaining admin
        if ($target->role === 'admin' && $validated['role'] !== 'admin') {
            if (User::where('role', 'admin')->count() <= 1) {
                return redirect()->route('users')->with('fail', 'You cannot demote the last remaining admin.');
            }
        }

        $data = [
            'name'         => $validated['full_name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'role'         => $validated['role'],
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $target->update($data);
        $from = $request->input('from', 'users');
        $redirectTo = $from === 'customers' ? route('customers') : route('users');

        return redirect($redirectTo)->with('success', 'User updated successfully.');
    }

    public function deleteUser($id)
    {
        $currentId = auth()->id();
        $user      = User::findOrFail($id);

        // safety checks (same as before)
        if ($user->id === $currentId) {
            return redirect()->route('users')
                ->with('fail', 'You cannot delete your own account.');
        }

        if ($user->role === 'admin' && User::where('role', 'admin')->count() <= 1) {
            return redirect()->route('users')
                ->with('fail', 'You cannot delete the last remaining admin.');
        }

        DB::transaction(function () use ($user) {
            // 1) Find related Customer (if any)
            if (method_exists($user, 'customer')) {
                $customer = $user->customer ?: Customer::where('user_id', $user->id)->first();
            } else {
                $customer = Customer::where('user_id', $user->id)->first();
            }

            // 2) Collect all orders tied to this user and (optionally) its customer record
            $orderQuery = Order::query()->where('user_id', $user->id);

            if ($customer) {
                $orderQuery->orWhere('customer_id', $customer->id);
            }

            $orders = $orderQuery->get();

            // 3) Delete order items first (if relation exists), then orders
            foreach ($orders as $order) {
                if (method_exists($order, 'items')) {
                    $order->items()->delete();   // order_items table, if present
                }
                $order->delete();
            }

            // 4) Delete the Customer record itself
            if ($customer) {
                $customer->delete();
            }

            // 5) Finally delete the User
            $user->delete();
        });

        return redirect()->route('users')
            ->with('success', 'User, their customer profile, and all related orders were deleted.');
    }


    /* ---------- Hide / Unhide ---------- */
    public function hide($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return back()->with('fail', 'You cannot hide your own account.');
        }
        if ($user->role === 'admin') {
            $adminCount = User::where('role', 'admin')->where('is_hidden', false)->count();
            if ($adminCount <= 1) {
                return back()->with('fail', 'You cannot hide the last visible admin.');
            }
        }

        $user->update(['is_hidden' => true]);

        // Mirror to Customer relation if present
        if (method_exists($user, 'customer') && $user->customer) {
            $user->customer->update(['is_hidden' => true]);
        } elseif (method_exists($user, 'customer')) {
            $user->customer()->create([
                'name'         => $user->name,
                'email'        => $user->email,
                'phone_number' => $user->phone_number,
                'address'      => $user->address,
                'is_hidden'    => true,
            ]);
        }

        return back()->with('success', 'User hidden.');
    }

    public function unhide($id)
    {
        $user = User::findOrFail($id);
        $user->update(['is_hidden' => false]);

        if (method_exists($user, 'customer') && $user->customer) {
            $user->customer->update(['is_hidden' => false]);
        }

        return back()->with('success', 'User unhidden.');
    }

    /* ---------- Block / Unblock ---------- */
    public function toggleBlock($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === auth()->id()) {
            return redirect()->route('users')->with('fail', 'You cannot block your own account.');
        }

        if ($user->role === 'admin' && $user->status === 'active') {
            if (User::where('role', 'admin')->count() <= 1) {
                return redirect()->route('users')->with('fail', 'You cannot block the last remaining admin.');
            }
        }

        $user->status = $user->status === 'active' ? 'blocked' : 'active';
        $user->save();

        return redirect()->route('users')->with('success', "User status updated to {$user->status}");
    }

    /* ---------- User self-service (from partner, merged) ---------- */
    public function loyalty_membemship() // keeping partner’s route name
    {
        $user = auth()->user();

        // 1) Count completed orders that qualify for loyalty
        $completedCount = $user->orders()
            ->where('status', 'completed')
            ->count();

        // 2) Total freebies ever earned (lifetime) = 1 per 10 completed orders
        $totalEarned = intdiv($completedCount, 10);

        // 3) How many the user has already claimed (from users.loyalty_claims)
        $claimed = (int) ($user->loyalty_claims ?? 0);

        // 4) What is still available to claim (this is the counter in the UI)
        $freeEarned = max($totalEarned - $claimed, 0);

        // 5) Progress toward the next free service
        $progress = $completedCount % 10;
        $nextIn   = $progress === 0 ? 10 : (10 - $progress);

        $recent = $user->orders()
            ->latest()
            ->take(10)
            ->get([
                'id',
                'created_at',
                'status',
                'total',
                'pickup_date',
                'pickup_time',
                'service_type',
                'load_qty',
                'payment_status',
            ]);

        $totalOrders = $completedCount;

        return view('user.loyaltyMembership.loyaltyMembership', compact(
            'user',
            'totalOrders',
            'freeEarned',   // available freebies
            'progress',
            'nextIn',
            'recent',
            'totalEarned',
            'claimed'
        ));
    }

    public function claimLoyaltyReward(Request $request)
    {
        $user = $request->user();

        // Recalculate based on current data
        $completedCount = $user->orders()
            ->where('status', 'completed')
            ->count();

        $totalEarned = intdiv($completedCount, 10);
        $claimed     = (int) ($user->loyalty_claims ?? 0);
        $available   = max($totalEarned - $claimed, 0);

        if ($available < 1) {
            return back()->with('fail', 'You have no free loads to claim yet.');
        }

        // Increment "claimed" by 1 → effectively decrements available freebies
        $user->loyalty_claims = $claimed + 1;
        $user->save();

        // 🔴 NEW: mark that ONE free load is applied to the *next* order only
        session(['loyalty_claims_for_current_order' => 1]);

        return back()->with(
            'success',
            'Free load loyalty claim recorded. It will be applied to your next order.'
        );
    }




    public function editOwnProfile()
    {
        $user = auth()->user();
        return view('user.dashboard.editUserProfileInfo', compact('user'));
    }

    public function updateOwnProfile(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'name'          => ['required', 'string', 'max:255'],
            'email'         => ['required', 'email', Rule::unique('users', 'email')->ignore($user->id)],
            'phone_number'  => ['required', 'string', 'max:20', Rule::unique('users', 'phone_number')->ignore($user->id)],
            'address'       => ['required', 'string', 'max:255'],
            'password'      => ['nullable', 'confirmed', 'min:8'],
        ]);

        $data = [
            'name'         => $validated['name'],
            'email'        => $validated['email'],
            'phone_number' => $validated['phone_number'],
            'address'      => $validated['address'],
        ];
        if (!empty($validated['password'])) {
            $data['password'] = Hash::make($validated['password']);
        }

        $user->update($data);
        return back()->with('success', 'Profile updated successfully.');
    }

    public function customerLoyalty(User $user)
    {
        // Count completed orders (these count toward the free service)
        $completed = $user->orders()
            ->where('status', 'completed')
            ->count();

        // Loyalty math: 1 free service per 10 completed orders
        $earned   = intdiv($completed, 10);
        $progress = $completed % 10;
        $toNext   = ($progress === 0) ? 10 : (10 - $progress);

        // Show the 10 most recent orders
        $recent = $user->orders()
            ->latest()
            ->take(10)
            ->get([
                'id',
                'created_at',
                'status',
                'total',
                'pickup_date',
                'pickup_time',
                'service_type',
                'load_qty',
                'payment_status'
            ]);

        return view('admin.customers.loyalty', compact(
            'user',
            'completed',
            'earned',
            'progress',
            'toNext',
            'recent'
        ));
    }

    public function customerOrders(Request $request, User $user)
    {
        // Read filters from query string
        $sort = $request->input('sort', 'desc'); // 'asc', 'desc', or 'all'
        $date = $request->input('date');         // Y-m-d or null

        // Base query: all orders that belong to this user
        $query = $user->orders()->with(['customer']);

        // If sort = "all", we IGNORE any date filter (behaves like index)
        if ($sort !== 'all' && !empty($date)) {
            // Filter by pickup_date (same behaviour as index history)
            $query->whereDate('pickup_date', $date);
            // If you prefer created_at instead, change to:
            // $query->whereDate('created_at', $date);
        }

        // Apply sort direction
        if ($sort === 'asc') {
            $query->orderBy('created_at', 'asc');
        } else {
            // For 'desc' and 'all', show newest first
            $query->orderBy('created_at', 'desc');
        }

        // Paginate and keep the filters in the links
        $orders = $query->paginate(15)->appends([
            'sort' => $sort,
            'date' => $date,
        ]);

        return view('admin.customers.orders', compact('user', 'orders'));
    }
}
