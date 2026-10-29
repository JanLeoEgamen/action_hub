<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Department;
use App\Models\Group;

class GroupSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $engineering = Department::where('department_name', 'Engineering')->first();
        $hr = Department::where('department_name', 'Human Resources')->first();
        $sales = Department::where('department_name', 'Sales')->first();
        $marketing = Department::where('department_name', 'Marketing')->first();
        $product = Department::where('department_name', 'Product')->first();
        $support = Department::where('department_name', 'Customer Support')->first();
        $operations = Department::where('department_name', 'Operations')->first();
        $consulting = Department::where('department_name', 'Consulting')->first();

        if (! $engineering || ! $hr || ! $sales || ! $marketing || ! $product || ! $support || ! $operations || ! $consulting) {
            $this->command->warn('GroupSeeder: departments not seeded. Run DepartmentSeeder first.');
            return;
        }

        $groups = [
            // Engineering
            [
                'department_id' => $engineering->id,
                'group_name' => 'Backend',
                'group_description' => 'API, database, and backend services.',
                'is_active' => true,
            ],
            [
                'department_id' => $engineering->id,
                'group_name' => 'Frontend',
                'group_description' => 'UI/UX and client-side development.',
                'is_active' => true,
            ],
            [
                'department_id' => $engineering->id,
                'group_name' => 'DevOps',
                'group_description' => 'Infrastructure, CI/CD, and deployment.',
                'is_active' => true,
            ],
            // Human Resources
            [
                'department_id' => $hr->id,
                'group_name' => 'Recruiting',
                'group_description' => 'Talent acquisition and hiring.',
                'is_active' => true,
            ],
            [
                'department_id' => $hr->id,
                'group_name' => 'Payroll',
                'group_description' => 'Compensation and benefits administration.',
                'is_active' => true,
            ],
            // Sales
            [
                'department_id' => $sales->id,
                'group_name' => 'Enterprise Sales',
                'group_description' => 'Large account sales and negotiations.',
                'is_active' => true,
            ],
            [
                'department_id' => $sales->id,
                'group_name' => 'SMB Sales',
                'group_description' => 'Small and mid-sized business sales.',
                'is_active' => true,
            ],
            // Marketing
            [
                'department_id' => $marketing->id,
                'group_name' => 'Content Marketing',
                'group_description' => 'Blog, social media, and content creation.',
                'is_active' => true,
            ],
            [
                'department_id' => $marketing->id,
                'group_name' => 'Demand Generation',
                'group_description' => 'Lead generation and demand campaigns.',
                'is_active' => true,
            ],
            // Product
            [
                'department_id' => $product->id,
                'group_name' => 'Product Management',
                'group_description' => 'Product strategy and feature planning.',
                'is_active' => true,
            ],
            [
                'department_id' => $product->id,
                'group_name' => 'Product Design',
                'group_description' => 'UX design and prototyping.',
                'is_active' => true,
            ],
            // Customer Support
            [
                'department_id' => $support->id,
                'group_name' => 'Technical Support',
                'group_description' => 'Technical troubleshooting and escalation.',
                'is_active' => true,
            ],
            [
                'department_id' => $support->id,
                'group_name' => 'Customer Success',
                'group_description' => 'Onboarding and account health.',
                'is_active' => true,
            ],
            // Operations
            [
                'department_id' => $operations->id,
                'group_name' => 'Logistics',
                'group_description' => 'Shipping, receiving, and inventory.',
                'is_active' => true,
            ],
            // Consulting
            [
                'department_id' => $consulting->id,
                'group_name' => 'Advisory',
                'group_description' => 'Strategic consulting engagements.',
                'is_active' => true,
            ],
        ];

        foreach ($groups as $data) {
            Group::createGroup($data);
        }
    }
}
