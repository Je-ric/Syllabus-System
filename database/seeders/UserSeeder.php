<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            [
                'name' => 'System Admin',
                'email' => 'admin@csms.local',
                'password' => 'password123',
                'role' => 'admin',
            ],
            [
                'name' => 'Department Chair',
                'email' => 'chair@csms.local',
                'password' => 'password123',
                'role' => 'chair',
            ],
            [
                'name' => 'College Dean',
                'email' => 'dean@csms.local',
                'password' => 'password123',
                'role' => 'dean',
            ],
            [
                'name' => 'Faculty User',
                'email' => 'faculty@csms.local',
                'password' => 'password123',
                'role' => 'faculty',
            ],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make($data['password']),
                    'account_status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $role = Role::where('name', $data['role'])->first();

            if ($role && !$user->roles()->where('name', $role->name)->exists()) {
                $user->roles()->attach($role);
            }
        }

        $this->command->info('Default users seeded successfully.');
    }
}
