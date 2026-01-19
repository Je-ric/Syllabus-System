<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@csms.local'], // prevent duplicates
            [
                'name' => 'Admin User',
                'password' => Hash::make('password123'), // change to strong password
                'account_status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Get or create admin role
        $adminRole = Role::firstOrCreate(['name' => 'admin']);

        // Assign role to admin
        if (!$admin->roles()->where('name', 'admin')->exists()) {
            $admin->roles()->attach($adminRole);
        }

        $this->command->info('Admin user created: admin@csms.local / password123');
    }
}
