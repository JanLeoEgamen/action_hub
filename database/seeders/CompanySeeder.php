<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Company;

class CompanySeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $companies = [
            [
                'company_name' => 'Bounty Plus Inc.',
                'company_description' => 'Premium quality products and services.',
                'is_active' => true,
            ],
            [
                'company_name' => 'Bounty Fresh Inc',
                'company_description' => 'Fresh eggs',
                'is_active' => true,
            ],
            [
                'company_name' => 'Inoza Feeds',
                'company_description' => 'Feeds.',
                'is_active' => true,
            ],
            [
                'company_name' => 'BVA',
                'company_description' => 'Value added products.',
                'is_active' => true,
            ],
        ];

        foreach ($companies as $data) {
            Company::createCompany($data);
        }
    }
}
