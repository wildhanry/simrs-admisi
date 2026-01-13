<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin SIMRS',
            'email' => 'admin@simrs.local',
            'password' => Hash::make('password'),
            'role' => 'admin',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);

        // Create staff user
        User::create([
            'name' => 'Staff Admisi',
            'email' => 'staff@simrs.local',
            'password' => Hash::make('password'),
            'role' => 'staff',
            'is_active' => true,
            'email_verified_at' => now(),
        ]);
    }
}
