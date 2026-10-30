<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdminRole = Role::where('name', 'Super Admin')->where('guard_name', 'web')->first();
        $adminRole = Role::where('name', 'Admin')->where('guard_name', 'web')->first();
        $managerRole = Role::where('name', 'Manager')->where('guard_name', 'web')->first();
        $staffRole = Role::where('name', 'Staff')->where('guard_name', 'web')->first();
        $userRole = Role::where('name', 'User')->where('guard_name', 'web')->first();

        $users = [
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'email' => 'superadmin@example.com',
                'password' => 'password',
                'role' => $superAdminRole,
            ],
            [
                'first_name' => 'Admin',
                'last_name' => 'User',
                'email' => 'admin@example.com',
                'password' => 'password',
                'role' => $adminRole,
            ],
            [
                'first_name' => 'Manager',
                'last_name' => 'User',
                'email' => 'manager@example.com',
                'password' => 'password',
                'role' => $managerRole,
            ],
            [
                'first_name' => 'Staff',
                'last_name' => 'User',
                'email' => 'staff@example.com',
                'password' => 'password',
                'role' => $staffRole,
            ],
            [
                'first_name' => 'Jan Leo',
                'last_name' => 'Egamen',
                'email' => 'user1@example.com',
                'password' => 'user1@example.com',
                'role' => $userRole,
            ],
            [
                'first_name' => 'Jerald',
                'last_name' => 'Arzanan',
                'email' => 'user2@example.com',
                'password' => 'user2@example.com',
                'role' => $userRole,
            ],
            [
                'first_name' => 'Louisa',
                'last_name' => 'Santos',
                'email' => 'user3@example.com',
                'password' => 'user3@example.com',
                'role' => $userRole,
            ],
        ];

        foreach ($users as $index => $data) {
            $user = User::create([
                'first_name' => $data['first_name'],
                'last_name' => $data['last_name'],
                'email' => $data['email'],
                'password' => bcrypt($data['password']),
            ]);

            // The 1st seeded user gets the Super Admin role.
            if ($index === 0 && $data['role']) {
                $user->syncRoles([$data['role']]);
            } elseif ($data['role']) {
                $user->syncRoles([$data['role']]);
            }
        }
    }
}
