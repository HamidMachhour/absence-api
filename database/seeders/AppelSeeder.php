<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Appel;

use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class AppelSeeder extends Seeder
{
    public function run()
    {
         // Clear  table
         DB::table('Appel')->truncate();

        // Get all user IDs from the User table
        $userIds = UserRole::whereRelation('role','name','manager')->pluck('user_id')->toArray();
        $faker = FakerFactory::create();

        // Loop through user IDs and create a appel for each user
        foreach ($userIds as $userId) {
            $id=$faker->uuid;
            Appel::create([
                'id' =>$id,
                'userId' => $userId,
            ]);
        }
    }
}
