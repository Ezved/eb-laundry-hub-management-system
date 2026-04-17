<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Support\Capacity;

class PickupDetailsController extends Controller
{
    /**
     * Smart entry:
     * - If the user already has required pickup details, go straight to Booking.
     * - Otherwise, show the Pickup Details form (only once).
     *
     * Route (inside user group):
     * Route::get('/booking/start', [PickupDetailsController::class, 'start'])->name('booking.start');
     */
    public function start(Request $request)
    {
        // Capacity guard: do not let users proceed to booking if cap is already hit
        if (Capacity::isCapped()) {
            return redirect()->route('user.dashboard')
                ->with(
                    'fail',
                    "We’re already at today’s capacity of " . Capacity::DAILY_LIMIT . " loads (8:00 AM–8:00 PM). Please try again after 8:00 PM or choose another day. Thank you for understanding!"
                );
        }

        $user = Auth::user();

        if ($this->isProfileComplete($user)) {
            return redirect()->route('booking.show');
        }

        // First-time (or incomplete) -> show the form
        return view('user.dashboard.pickupDetails', ['user' => $user]);
    }

    /**
     * Manual open of the form. If profile is already complete,
     * bounce to booking so the form doesn't keep appearing.
     */
    public function edit()
    {
        // Capacity guard here as well (manual open)
        if (Capacity::isCapped()) {
            return redirect()->route('user.dashboard')
                ->with(
                    'fail',
                    "We’ve reached today’s limit of " . Capacity::DAILY_LIMIT . " loads (8:00 AM–8:00 PM). Kindly schedule after 8:00 PM or pick another day."
                );
        }

        $user = Auth::user();

        if ($this->isProfileComplete($user)) {
            return redirect()->route('booking.show');
        }

        return view('user.dashboard.pickupDetails', compact('user'));
    }

    /**
     * Save pickup details, then always redirect to Booking.
     */
    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name'             => 'required|string|max:255',
            'email'            => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number'     => 'nullable|string|max:25',
            'address'          => 'required|string|max:500',
            'location_details' => 'nullable|string|max:500',
        ]);

        $user->update($request->only([
            'name', 'email', 'phone_number', 'address', 'location_details',
        ]));

        // After saving (first time or subsequent edits), go straight to Booking
        return redirect()->route('booking.show')->with('status', 'Pickup details saved.');
    }

    /**
     * Decide if the user can skip the pickup-details screen.
     * Tweak the required fields as you prefer.
     */
    private function isProfileComplete(?User $user): bool
    {
        if (!$user) return false;

        return filled($user->name)
            && filled($user->email)
            && filled($user->address)
            && filled($user->phone_number); // make phone optional by removing this line if desired
    }
}
