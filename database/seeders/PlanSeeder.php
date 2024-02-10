<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Plan;
use Illuminate\Support\Str;

class PlanSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        $plans = [
            [
                'name' => 'Basic (Free) Plan',
                'description' => 'Maximum Classes: 1, Maximum students per Store: 100 (Invisible), Fee for Finalized Orders: None (No charge for finalized absences), Basic Analytics and Reporting, Standard Customer Support, Limited Customization Options, No Premium Features',
                'fee' => 0,
                'max_classes' => 1,
                'max_students' => 100,
            ],
            [
                'name' => 'Standard Plan',
                'description' => 'Maximum Classes: 5, Maximum students per Store: 500 (Visible), Fee for Finalized Orders: 2% of the absence amount, Advanced Analytics and Reporting, Priority Customer Support, Basic Customization Options, Integrated Payment Gateways, Product Reviews and Ratings',
                'fee' => 2,
                'max_classes' => 5,
                'max_students' => 500,
            ],
            [
                'name' => 'Pro Plan',
                'description' => 'Maximum Classes: 20, Maximum students per Store: 2,000 (Visible), Fee for Finalized Orders: 1.5% of the absence amount, Comprehensive Analytics and Reporting, Dedicated Customer Support, Advanced Customization Options, Integrated Payment Gateways with Advanced Features (e.g., recurring payments), Marketing and Promotional Tools, Multi-Language Support',
                'fee' => 1.5,
                'max_classes' => 20,
                'max_students' => 2000,
            ],
            [
                'name' => 'Enterprise Plan',
                'description' => 'Customizable based on specific requirements, Unlimited Manech Stores and Items, Negotiable Fee for Finalized Orders, Tailored Analytics and Reporting, Priority Customer Support with Dedicated Account Manager, Advanced Customization Options and Branding, Advanced Marketing and SEO Tools, Custom Integrations and API Access, Enterprise-grade Security and Compliance',
                'fee' => 0,
                'max_classes' => 0,
                'max_students' => 0,
            ],
        ];

        foreach ($plans as $plan) {
            $plan['id']=Str::uuid();
            Plan::create($plan);
        }
    }
}
