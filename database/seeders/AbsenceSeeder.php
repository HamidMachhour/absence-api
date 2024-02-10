<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Seance;
use App\Models\Absence;
use App\Models\Address;
use App\Models\Matiere;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class AbsenceSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        DB::table('Absence')->truncate();
        // Get all user IDs from the User table
        $userIds = UserRole::whereRelation('role','name','student')->pluck('user_id')->toArray();
        $faker = FakerFactory::create();

        $seanceIds=Seance::pluck('id')->toArray();
        // Loop through user IDs and create 10 absences for each user
        foreach ($userIds as $userId) {

            $addressIds = Address::where('userId', $userId)->pluck('id')->toArray();
            // Generate 10 random absences
            for ($i = 1; $i <= 10; $i++) {
                $id=$faker->uuid();
                DB::table('Absence')->insert([
                    'id' => $id,
                    'userId' => $userId,
                    'seanceId' => $faker->randomElement($seanceIds),
                    'created_at' => now()->subDays(rand(31, 365)),
/*                     'deleted_at' => $faker->randomElement([null,now()->subDays(rand(0, 30))]),
 */                ]);
            }
        }
    }
}
