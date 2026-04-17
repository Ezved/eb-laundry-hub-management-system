<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\PickupDetailsController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderSummaryController;
use App\Http\Controllers\AdminOrderController;
use App\Http\Controllers\AdminCalendarController;
use App\Http\Controllers\AdminWalkinController;
use App\Http\Controllers\FeedbackController;
use App\Http\Controllers\AdminFeedbackController;
use App\Http\Controllers\UserNotificationController;
use App\Http\Controllers\SalesController;
use App\Support\Capacity;
use App\Http\Controllers\CustomerEmailNotificationController; // <-- added
use App\Http\Controllers\SettingsController; // make sure this is at the top


/*
|--------------------------------------------------------------------------
| Public / Home
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    $services = \App\Models\Service::where('is_active', true)
        ->orderBy('sort_order')
        ->orderBy('title')
        ->get();

    $feedbacks = \App\Models\Feedback::where('is_visible', true)
        ->latest()
        ->take(9)
        ->get();

    return view('home', compact('services', 'feedbacks'));
})->name('home');

// Public-facing feedback create (requires auth to post)
Route::post('/feedback', [FeedbackController::class, 'store'])
    ->middleware('auth')
    ->name('feedback.store');

// Capacity check (public)
Route::get('/capacity/today', function () {
    return response()->json([
        'ok'     => true,
        'capped' => Capacity::isCapped(),
        'loads'  => Capacity::todaysLoads(),
        'limit'  => Capacity::DAILY_LIMIT,
    ]);
})->name('capacity.today');

/*
|--------------------------------------------------------------------------
| Auth (Login / Register / Logout)
|--------------------------------------------------------------------------
*/
Route::get('/register', [AuthenticationController::class, 'loadRegisterForm'])->name('register');
Route::post('/register', [AuthenticationController::class, 'registerUser'])->name('register.post');

Route::get('/login', [AuthenticationController::class, 'loadLoginForm'])->name('login');
Route::post('/login', [AuthenticationController::class, 'loginUser'])->name('login.post');

Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');

/*
|--------------------------------------------------------------------------
| User area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:user', 'prevent-back-history'])->group(function () {
    // Dashboard
    Route::get('user/dashboard', [OrderController::class, 'myOrders'])->name('user.dashboard');

    // Orders (view → summary page)
    Route::get('/orders/{order}', [OrderSummaryController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/cancel', [OrderController::class, 'cancel'])->name('orders.cancel');
    Route::delete('/orders/{order}', [OrderController::class, 'destroy'])->name('orders.destroy');
    // Receipt
    Route::get('/orders/{order}/receipt', [OrderController::class, 'receipt'])->name('orders.receipt');

    // Reschedule (user)
    Route::get('/orders/{order}/reschedule', [OrderController::class, 'reschedule'])
        ->name('order.reschedule');

    Route::post('/orders/{order}/reschedule', [OrderController::class, 'submitReschedule'])
        ->name('order.reschedule.submit');

    // Pickup Details
    Route::view('user/pickupDetails', 'user.dashboard.pickupDetails')->name('user.pickupDetails');
    Route::get('/pickup/details',  [PickupDetailsController::class, 'edit'])->name('pickup.edit');
    Route::post('/pickup/details', [PickupDetailsController::class, 'update'])->name('pickup.update');

    // Booking flow
    Route::get('/booking', fn() => view('user.dashboard.booking'))->name('booking.show');
    Route::get('/booking/start', [PickupDetailsController::class, 'start'])->name('booking.start');
    Route::post('/order/summary', [OrderSummaryController::class, 'summary'])->name('order.summary');
    Route::post('/order/confirm', [OrderController::class, 'confirm'])->name('order.confirm');
    // Feedback (form + delete)
    Route::get('/user/feedback', [FeedbackController::class, 'create'])->name('user.feedback');
    Route::delete('/feedback/{feedback}', [FeedbackController::class, 'destroy'])->name('feedback.destroy');

    // 🔴 NEW: thank-you page after successful confirm
    Route::get('/order/thank-you/{order}', [OrderController::class, 'thankYou'])
        ->name('order.thankyou');

    // Notifications
    Route::get('/notifications/poll', [UserNotificationController::class, 'poll'])->name('user.notifications.poll');
    Route::post('/notifications/{notification}/read', [UserNotificationController::class, 'markRead'])->name('user.notifications.read');

    // Order History (canonical + legacy alias)
    Route::get('user/orders/history', [OrderController::class, 'orderHistory'])->name('user.orders.history');
    Route::get('user/order/history',  [OrderController::class, 'orderHistory'])->name('user.orderHistory');

    // Loyalty Membership
    Route::get('/user/loyalty/membership', [UserController::class, 'loyalty_membemship'])->name('loyalty.membership');
    Route::post('/user/loyalty/membership/claim', [UserController::class, 'claimLoyaltyReward'])
        ->name('loyalty.claim');

    // Capacity check (AJAX)
    // Route::get('/capacity/today', [CapacityController::class, 'today'])->name('capacity.today');
});

/*
|--------------------------------------------------------------------------
| Admin area
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin', 'prevent-back-history'])->group(function () {
    // Dashboard + Orders
    Route::get('admin/dashboard', [AdminOrderController::class, 'today'])->name('admin.dashboard');
    Route::patch('admin/orders/{order}/payment', [AdminOrderController::class, 'updatePayment'])->name('admin.orders.payment');
    Route::patch('admin/orders/{order}/status',  [AdminOrderController::class, 'updateStatus'])->name('admin.orders.status');
    Route::delete('admin/orders/{order}',        [AdminOrderController::class, 'destroy'])->name('admin.orders.destroy');
    Route::get('admin/orders/{order}',           [AdminOrderController::class, 'show'])->name('admin.orders.show');
    Route::get('admin/orders/{order}/receipt',   [AdminOrderController::class, 'receipt'])->name('admin.orders.receipt');
    Route::patch('/admin/orders/{order}/hide', [AdminOrderController::class, 'hide'])
    ->name('admin.orders.hide');
    // routes/web.php
    // Dashboard + Orders added 11/13/2025
    Route::get('admin/dashboard', [AdminOrderController::class, 'today'])->name('admin.dashboard');

    /* 11/13/2025 */
    Route::get('admin/orders', [AdminOrderController::class, 'index'])->name('admin.orders.orderHistory');

    // Admin: prepare receipt (final load, service, total) before showing printable receipt
    Route::post('admin/orders/{order}/receipt/prepare', [AdminOrderController::class, 'prepareReceipt'])
        ->name('admin.orders.receipt.prepare');   // STEP 1: from modal

    Route::post('admin/orders/{order}/receipt/send', [AdminOrderController::class, 'sendReceipt'])
        ->name('admin.orders.receipt.send');
 Route::get('/profile', [SettingsController::class, 'editAdmin'])
            ->name('admin.dashboard.editAdminProfileInfo');


    // Walk-in booking
    Route::get('/walkin/create',   [AdminWalkinController::class, 'create'])->name('admin.walkin.create');
    Route::post('/walkin/preview', [AdminWalkinController::class, 'preview'])->name('admin.walkin.preview');
    Route::post('/walkin/store',   [AdminWalkinController::class, 'store'])->name('admin.walkin.store');
    Route::get('/admin/walkin/customer/{customer}', [AdminWalkinController::class, 'createFromCustomer'])
        ->name('admin.customers.from_customer');

    // Calendar
    Route::get('/admin/calendar/events',  [AdminCalendarController::class, 'events'])->name('admin.calendar.events');
    Route::post('/admin/calendar/notify', [AdminCalendarController::class, 'notify'])->name('admin.calendar.notify');

    // Customers
    Route::get('/admin/customers',                   [CustomerController::class, 'index'])->name('customers');
    Route::get('/admin/customers/search/ajax',       [CustomerController::class, 'ajaxSearch'])->name('customers.search.ajax');
    Route::get('/admin/customers/hide/{customer}',   [CustomerController::class, 'hide'])->name('customers.hide');
    Route::get('/admin/customers/unhide/{customer}', [CustomerController::class, 'unhide'])->name('customers.unhide');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}',        [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}',     [CustomerController::class, 'destroy'])->name('customers.destroy');

    // Delivery Calendar page
    Route::get('admin/deliver-calendar', function () {return view('admin.calendar.deliveryCalendar');})->name('admin.delivery.calendar');

    // Sales Report page
    Route::get('admin/sales-report', function () {return view('admin.sales.salesReport');})->name('admin.sales.report');

    // Users
    Route::get('/users',             [UserController::class, 'loadAllUsers'])->name('users');
    Route::get('/add/user',          [UserController::class, 'loadaddUserForm']);
    Route::post('/add/user',         [UserController::class, 'addUser'])->name('user.add');
    Route::get('/edit/{id}',         [UserController::class, 'loadEditForm'])->whereNumber('id');
    Route::post('/edit/user',        [UserController::class, 'editUser'])->name('user.edit');
    Route::get('/users/{id}/hide',   [UserController::class, 'hide'])->whereNumber('id')->name('users.hide');
    Route::get('/users/{id}/unhide', [UserController::class, 'unhide'])->whereNumber('id')->name('users.unhide');
    Route::get('/users/search/ajax', [UserController::class, 'ajaxSearch'])->name('users.search.ajax');
    Route::get('/toggle-block/{id}', [UserController::class, 'toggleBlock'])->whereNumber('id')->name('users.toggleBlock');
    Route::get('/admin/customers/{customer}/orders', [CustomerController::class, 'orders'])->name('customers.orders');
    Route::get('/admin/customers/{customer}/loyalty', [CustomerController::class, 'loyalty'])->name('customers.loyalty');

    // Services
    Route::get('/services',                   [ServiceController::class, 'list'])->name('services');
    Route::get('/add/service',                [ServiceController::class, 'addForm'])->name('service.addForm');
    Route::post('/add/service',               [ServiceController::class, 'add'])->name('service.add');
    Route::get('/edit/service/{id}',          [ServiceController::class, 'editForm'])->whereNumber('id')->name('service.editForm');
    Route::post('/edit/service',              [ServiceController::class, 'edit'])->name('service.edit');
    Route::get('/delete/service/{id}',        [ServiceController::class, 'delete'])->whereNumber('id')->name('service.delete');
    Route::get('/admin/services/search/ajax', [ServiceController::class, 'ajaxSearch'])->name('services.search.ajax');


    // Feedback management
    Route::get('/admin/feedbacks',                     [AdminFeedbackController::class, 'index'])->name('admin.feedback.index');
    Route::get('/admin/feedbacks/hide/{feedback}',     [AdminFeedbackController::class, 'hide'])->name('admin.feedback.hide');
    Route::get('/admin/feedbacks/unhide/{feedback}',   [AdminFeedbackController::class, 'unhide'])->name('admin.feedback.unhide');
    Route::delete('/admin/feedbacks/{feedback}',       [AdminFeedbackController::class, 'destroy'])->name('admin.feedback.destroy');

    // ✅ RENAMED: Admin-specific customer drill-downs (using AdminOrderController)
    Route::get('/admin/customers/{user}/orders-admin',  [AdminOrderController::class, 'customerOrders'])->name('admin.customers.orders');
    Route::get('/admin/customers/{user}/loyalty-admin', [UserController::class, 'customerLoyalty'])->name('admin.customers.loyalty');

    // Sales (admin)
    Route::get('/sales-report', [SalesController::class, 'index'])->name('admin.sales.report');
    Route::get('/sales-report/export', [SalesController::class, 'export'])->name('admin.sales.report.export.excel');
    Route::get('/admin/sales/summary',  [SalesController::class, 'summary'])->name('admin.sales.summary');
    Route::get('/admin/sales/total',    [AdminOrderController::class, 'salesTotal'])->name('admin.sales.total');
});

Route::get('/orders/{order}/receipt', [AdminOrderController::class, 'showReceipt'])
    ->name('orders.receipt.show');

/*
|--------------------------------------------------------------------------
| Shared (Profile / Settings) — any authenticated user
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'prevent-back-history'])->group(function () {
    Route::get('/settings', [UserController::class, 'editOwnProfile'])->name('settings.edit');
    Route::put('/settings', [UserController::class, 'updateOwnProfile'])->name('settings.update');
});

/*
|--------------------------------------------------------------------------
| Social auth route files (load if present)
|--------------------------------------------------------------------------
*/
if (file_exists(base_path('routes/googleAuth.php'))) {
    require base_path('routes/googleAuth.php');
}
if (file_exists(base_path('routes/facebookAuth.php'))) {
    require base_path('routes/facebookAuth.php');
}

// Public, signed receipt link (no auth; signature protects it)
Route::get('/receipt/{order}', [OrderController::class, 'publicReceipt'])
    ->name('orders.receipt.public')
    ->middleware('signed');

if (file_exists(base_path('routes/forgotPassword.php'))) { // 11/16/2025
    require base_path('routes/forgotPassword.php');
}

Route::get('/verify-email/{user}', [AuthenticationController::class, 'verifyEmail']) // added 11/17/2025
    ->name('verify.email')
    ->middleware('signed');   // protects the URL

// routes/web.php
// (put this just under your existing login routes)
Route::get('/login', [AuthenticationController::class, 'loadLoginForm'])->name('login');
Route::post('/login', [AuthenticationController::class, 'loginUser'])->name('login.post');

Route::post('/resend-verification', [AuthenticationController::class, 'resendVerification'])
    ->name('verification.resend');

Route::post('/logout', [AuthenticationController::class, 'logout'])
    ->name('logout')
    ->middleware('auth');
