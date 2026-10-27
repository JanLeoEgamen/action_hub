<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles and the permissions each should hold.
        $rolePermissions = [
            'Super Admin' => null, // null means all permissions (handled by Gate::before)
            'Admin' => [
                'view companies', 'create companies', 'edit companies', 'delete companies',
                'view departments', 'create departments', 'edit departments', 'delete departments',
                'view groups', 'create groups', 'edit groups', 'delete groups',
                'view users', 'create users', 'edit users', 'delete users',
                'view roles', 'create roles', 'edit roles', 'delete roles',
                'view task categories', 'create task categories', 'edit task categories', 'delete task categories',
                'view projects', 'create projects', 'edit projects', 'delete projects',
                'view tasks', 'create tasks', 'edit tasks', 'delete tasks',
                'view tickets', 'create tickets', 'edit tickets', 'delete tickets',
            ],
            'Manager' => [
                'view companies', 'view departments', 'view groups',
                'view users', 'edit users',
                'view roles',
                'view task categories',
                'view projects', 'create projects', 'edit projects',
                'view tasks', 'create tasks', 'edit tasks',
                'view tickets', 'create tickets', 'edit tickets',
            ],
            'Staff' => [
                'view companies',
                'view departments',
                'view groups',
                'view users',
                'view task categories',
                'view projects',
                'view tasks',
                'view tickets',
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissions) {
            $role = Role::firstOrCreate(['name' => $roleName, 'guard_name' => 'web']);

            if ($permissions === null) {
                // Super Admin gets every permission explicitly (Gate::before also bypasses).
                $role->syncPermissions(Permission::all());
            } else {
                $role->syncPermissions($permissions);
            }
        }
    }
}
