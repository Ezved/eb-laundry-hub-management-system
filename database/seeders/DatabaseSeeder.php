<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database  php artisan make:controller SettingsController
.
     */
    public function run(): void
    {
        // Test user – won't conflict anymore
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name'              => 'Test User',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),
                'role'              => 'user',      // or 'customer', doesn't matter for login
                'status'            => 'active',    // IMPORTANT if you use status in login
            ]
        );

        // Your own account
        User::updateOrCreate(
            ['email' => 'orenedsenajon@gmail.com'],
            [
                'name'              => 'Oren',
                'password'          => Hash::make('password'),
                'email_verified_at' => now(),      // marks as verified
                'role'              => 'user',      // or 'customer'
                'status'            => 'active',    // so Auth::attempt finds it
            ]
        );

        // Other seeders
        $this->call(AdminSeeder::class);
        $this->call(CoreServicesSeeder::class);
    }
}
