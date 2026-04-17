<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create the admin if missing
        $admin = User::firstOrCreate(
            ['email' => 'eblaundry@gmail.com'],
            [
                'name'         => 'E&B Laundry Admin',
                'phone_number' => '09091753528',
                'password'     => Hash::make('password'), // change in production
                'role'         => 'admin',
            ]
        );

        // ALWAYS ensure status + email_verified_at are set correctly
        $admin->forceFill([
            'status'            => 'active',   // must pass your login check
            'email_verified_at' => now(),      // treat admin as already verified
        ])->save();
    }
}