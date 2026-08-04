<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UniversityUserSeeder extends Seeder
{
    public function run(): void
    {
        $users = [
            ['name' => 'Juan Dela Cruz',       'email' => 'juan.delacruz@clsu.edu.ph',       'role' => 'admin'],
            ['name' => 'Maria Santos',         'email' => 'maria.santos@clsu.edu.ph',         'role' => 'ovpaa'],
            ['name' => 'Jose Ramirez',         'email' => 'jose.ramirez@clsu.edu.ph',         'role' => 'dean'],
            ['name' => 'Angela Reyes',         'email' => 'angela.reyes@clsu.edu.ph',         'role' => 'dean'],
            ['name' => 'Michael Cruz',         'email' => 'michael.cruz@clsu.edu.ph',         'role' => 'dean'],
            ['name' => 'Patricia Flores',      'email' => 'patricia.flores@clsu.edu.ph',      'role' => 'chair'],
            ['name' => 'Daniel Mendoza',       'email' => 'daniel.mendoza@clsu.edu.ph',       'role' => 'chair'],
            ['name' => 'Jennifer Garcia',      'email' => 'jennifer.garcia@clsu.edu.ph',      'role' => 'chair'],
            ['name' => 'Kevin Navarro',        'email' => 'kevin.navarro@clsu.edu.ph',        'role' => 'chair'],
            ['name' => 'Sarah Bautista',       'email' => 'sarah.bautista@clsu.edu.ph',       'role' => 'faculty'],
            ['name' => 'John Paul Aquino',     'email' => 'johnpaul.aquino@clsu.edu.ph',      'role' => 'faculty'],
            ['name' => 'Nicole Torres',        'email' => 'nicole.torres@clsu.edu.ph',        'role' => 'faculty'],
            ['name' => 'Vincent Lopez',        'email' => 'vincent.lopez@clsu.edu.ph',        'role' => 'faculty'],
            ['name' => 'Christine Ramos',      'email' => 'christine.ramos@clsu.edu.ph',      'role' => 'faculty'],
            ['name' => 'Carlo Villanueva',     'email' => 'carlo.villanueva@clsu.edu.ph',     'role' => 'faculty'],
            ['name' => 'Rica Hernandez',       'email' => 'rica.hernandez@clsu.edu.ph',       'role' => 'faculty'],
            ['name' => 'Mark Anthony Perez',   'email' => 'mark.perez@clsu.edu.ph',           'role' => 'faculty'],
            ['name' => 'Elaine Castillo',      'email' => 'elaine.castillo@clsu.edu.ph',      'role' => 'faculty'],
            ['name' => 'Joshua Lim',           'email' => 'joshua.lim@clsu.edu.ph',           'role' => 'faculty'],
            ['name' => 'Alyssa Domingo',       'email' => 'alyssa.domingo@clsu.edu.ph',       'role' => 'faculty'],
        ];

        foreach ($users as $data) {
            $user = User::firstOrCreate(
                ['email' => $data['email']],
                [
                    'name' => $data['name'],
                    'password' => Hash::make('password123'),
                    'account_status' => 'active',
                    'email_verified_at' => now(),
                ]
            );

            $role = Role::where('name', $data['role'])->first();

            if ($role && ! $user->roles()->where('roles.id', $role->id)->exists()) {
                $user->roles()->attach($role);
            }
        }

        $this->command->info('20 university users seeded successfully.');
    }
}