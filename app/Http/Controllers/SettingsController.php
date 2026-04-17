<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class SettingsController extends Controller
{
    // --- USER PROFILE PAGE ---
    public function edit()
    {
        $user = auth()->user();

        // if your user edit view is: resources/views/user/dashboard/editProfile.blade.php
        return view('user.dashboard.editProfile', compact('user'));
        // adjust the view name above if your file name/path is different
    }

    // --- ADMIN PROFILE PAGE ---
    public function editAdmin()
{
    $user = auth()->user();
    return view('admin.dashboard.editAdminProfileInfo', compact('user'));
}



    // --- UPDATE PROFILE (USED BY BOTH USER + ADMIN FORMS) ---
    public function update(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'email'            => ['required', 'email', 'max:255'],
            'phone_number'     => ['required', 'string', 'max:50'],
            'address'          => ['required', 'string', 'max:255'],
            'location_details' => ['nullable', 'string', 'max:255'],
            'password'         => ['nullable', 'string', 'min:8', 'confirmed'],
        ]);

        $user->name             = $validated['name'];
        $user->email            = $validated['email'];
        $user->phone_number     = $validated['phone_number'];
        $user->address          = $validated['address'];
        $user->location_details = $validated['location_details'] ?? null;

        // Only change password if filled
        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        return back()->with('success', 'Profile updated successfully.');
    }
}
