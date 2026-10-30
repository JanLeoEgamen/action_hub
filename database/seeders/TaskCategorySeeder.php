<?php

namespace Database\Seeders;

use App\Models\TaskCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TaskCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Ad hoc',
                'description' => 'Need to be done ASAP',
                'is_active' => true,
            ],
            [
                'name' => 'Business As Usual',
                'description' => 'Regular business tasks',
                'is_active' => true,
            ],
           
        ];

        foreach ($categories as $data) {
            TaskCategory::create($data);
        }
    }
}
