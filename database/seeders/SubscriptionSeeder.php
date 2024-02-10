<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Plan;
use Carbon\Carbon;
use Illuminate\Support\Str;

class SubscriptionSeeder extends Seeder
{
    /**
     * Run the seeder.
     *
     * @return void
     */
    public function run()
    {
        // Get all users and plans
        $users = User::all();
        $plans = Plan::all();
        $statuses = ['active', 'expired', 'canceled'];
        // Iterate through users and assign a subscription
        foreach ($users as $user) {
            // Select a random plan
            $plan = $plans->random();

            // Calculate the expiration date based on the plan
            $expirationDate = Carbon::now()->addDays($plan->duration);

            // Select a random status
            $status = $statuses[array_rand($statuses)];

            // Create the subscription
            Subscription::create([
                'id' =>Str::uuid(),
                'userId' => $user->id,
                'planId' => $plan->id,
                'expire_at' => $expirationDate,
                'status' => $status,
            ]);
        }
    }
}

