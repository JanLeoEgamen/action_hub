<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // every module the UI gates with @can / can()
        $policies = [
            'companies', 'departments', 'groups', 'users', 'roles',
            'task categories', 'projects', 'tasks', 'tickets',
        ];

        $actions = ['view', 'create', 'edit', 'delete'];

        // the first pass seeded these singular; the app asks for the plural
        foreach (['user' => 'users'] as $singular => $plural) {
            foreach ($actions as $action) {
                $permission = Permission::where('name', $action . ' ' . $singular)
                    ->where('guard_name', 'web')
                    ->first();

                if ($permission) {
                    $permission->name = $action . ' ' . $plural;
                    $permission->save();          // rename keeps every existing assignment
                }
            }
        }

        foreach ($policies as $policy) {
            foreach ($actions as $action) {
                Permission::findOrCreate($action . ' ' . $policy, 'web');   // idempotent
            }
        }

        // Super Admin already bypasses via Gate::before; give it the full set explicitly
        if ($superAdmin = Role::where('name', 'Super Admin')->first()) {
            $superAdmin->syncPermissions(Permission::all());
        }
    }

}
