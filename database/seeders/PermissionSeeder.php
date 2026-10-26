<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $policies = ['user', 'roles', 'task categories', 'tasks', 'projects', 'tickets'];

        foreach ($policies as $policy) {
            $actions = ['view', 'create', 'edit', 'delete'];

            foreach ($actions as $action) {
                DB::table('permissions')->insert([
                    'name' => $action. ' ' . $policy ,
                    'guard_name' => 'web',
                    'created_at' => now(),
                ]);
            }
        }
    }

}
