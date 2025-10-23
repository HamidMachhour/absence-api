<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Attendance;

use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run()
    {
         // Clear  table
         //DB::table('Attendance')->truncate();

        // Get all user IDs from the User table
        $userIds = UserRole::whereRelation('role','name','manager')->pluck('userId')->toArray();
        $faker = FakerFactory::create();

        // Loop through user IDs and create a attendance for each user
        foreach ($userIds as $userId) {
            $id=$faker->uuid;
            Attendance::create([
                'id' =>$id,
                'userId' => $userId,
            ]);
        }
    }
}
