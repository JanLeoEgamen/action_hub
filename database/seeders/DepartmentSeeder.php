<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Department;

class DepartmentSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $bountyPlus = Company::where('company_name', 'Bounty Plus Inc.')->first();
        $bountyFresh = Company::where('company_name', 'Bounty Fresh Inc')->first();
        $inozaFeeds = Company::where('company_name', 'Inoza Feeds')->first();
        $bva = Company::where('company_name', 'BVA')->first();

        if (! $bountyPlus || ! $bountyFresh || ! $inozaFeeds || ! $bva) {
            $this->command->warn('DepartmentSeeder: companies not seeded. Run CompanySeeder first.');
            return;
        }

        $departments = [
            // Bounty Plus Inc.
            [
                'company_id' => $bountyPlus->id,
                'department_name' => 'Engineering',
                'department_description' => 'Software development and infrastructure.',
                'is_active' => true,
            ],
            [
                'company_id' => $bountyPlus->id,
                'department_name' => 'Human Resources',
                'department_description' => 'Recruiting, onboarding, and employee relations.',
                'is_active' => true,
            ],
            [
                'company_id' => $bountyPlus->id,
                'department_name' => 'Sales',
                'department_description' => 'Revenue generation and client acquisition.',
                'is_active' => true,
            ],
            [
                'company_id' => $bountyPlus->id,
                'department_name' => 'Marketing',
                'department_description' => 'Brand awareness and lead generation.',
                'is_active' => true,
            ],
            // Bounty Fresh Inc
            [
                'company_id' => $bountyFresh->id,
                'department_name' => 'Product',
                'department_description' => 'Product management and roadmap planning.',
                'is_active' => true,
            ],
            [
                'company_id' => $bountyFresh->id,
                'department_name' => 'Customer Support',
                'department_description' => 'Technical support and customer success.',
                'is_active' => true,
            ],
            // Inoza Feeds
            [
                'company_id' => $inozaFeeds->id,
                'department_name' => 'Operations',
                'department_description' => 'Logistics and supply chain management.',
                'is_active' => true,
            ],
            // BVA
            [
                'company_id' => $bva->id,
                'department_name' => 'Consulting',
                'department_description' => 'Client consulting and advisory services.',
                'is_active' => true,
            ],
        ];

        foreach ($departments as $data) {
            Department::createDepartment($data);
        }
    }
}
