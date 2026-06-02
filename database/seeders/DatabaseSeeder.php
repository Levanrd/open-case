<?php

namespace Database\Seeders;

use App\Models\ServicePackage;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(
            ['email' => 'admin@opencase.local'],
            [
                'first_name' => 'Open Case',
                'last_name' => 'Admin',
                'name' => 'Open Case Admin',
                'phone' => '+630000000000',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
            ],
        );

        User::updateOrCreate(
            ['email' => 'accountant@opencase.local'],
            [
                'first_name' => 'Internal',
                'last_name' => 'Accountant',
                'name' => 'Internal Accountant',
                'phone' => '+630000000001',
                'password' => Hash::make('password'),
                'role' => 'accountant',
                'email_verified_at' => now(),
            ],
        );

        collect([
            [
                'name' => 'Basic Readiness Review',
                'description' => 'Preliminary review of business profile, tax types, available documents, and next-step checklist.',
                'price' => 2500,
                'case_type' => null,
                'features' => ['Document checklist', 'Preliminary risk review', 'Next-step summary'],
                'sort_order' => 10,
            ],
            [
                'name' => 'Open Case Review',
                'description' => 'Accountant-assisted review for notices, missing filings, open case summaries, and cleanup priorities.',
                'price' => 6500,
                'case_type' => 'open_case_review',
                'features' => ['Assigned accountant', 'Notice review', 'Client-visible recommendations'],
                'sort_order' => 20,
            ],
            [
                'name' => 'Business Closure Assistance',
                'description' => 'Guided closure preparation for stopped businesses, document inventory, and RDO coordination readiness.',
                'price' => 12000,
                'case_type' => 'business_closure',
                'features' => ['Closure checklist', 'Receipt and books review', 'Final recommendation'],
                'sort_order' => 30,
            ],
            [
                'name' => 'Full Compliance Cleanup',
                'description' => 'High-priority support for multiple missing years, mixed tax types, notices, and closure readiness.',
                'price' => 22000,
                'case_type' => 'compliance_cleanup',
                'features' => ['Priority review', 'Missing-year map', 'Detailed cleanup plan'],
                'sort_order' => 40,
            ],
        ])->each(fn (array $package) => ServicePackage::updateOrCreate(
            ['name' => $package['name']],
            [...$package, 'active' => true],
        ));
    }
}
