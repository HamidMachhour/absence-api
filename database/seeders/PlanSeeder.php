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
        // Delete existing plans before seeding new ones
        // Plan::truncate();
        
        $plans = [
            [
            'name' => 'Basic (Free) Plan',
            'description' => 'Perfect for small schools: Manage up to 3 classes with 30 students each. Includes basic attendance tracking and reporting.',
            'fee' => 0,
            'max_classes' => 3,
            'max_students' => 30,
            ],
            [
            'name' => 'Standard Plan',
            'description' => 'Ideal for medium-sized schools: Manage up to 15 classes with 40 students each. Includes advanced reporting, priority support, and customizable attendance policies.',
            'fee' => 2,
            'max_classes' => 15,
            'max_students' => 40,
            ],
            [
            'name' => 'Pro Plan',
            'description' => 'Designed for large schools: Manage up to 50 classes with 45 students each. Includes real-time attendance tracking, automated reports, SMS notifications, and advanced analytics.',
            'fee' => 1.5,
            'max_classes' => 50,
            'max_students' => 45,
            ],
            [
            'name' => 'Enterprise Plan',
            'description' => 'For educational institutions of any size. Unlimited classes and students, custom integrations with school management systems, dedicated support, and advanced security features.',
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
