<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\UserRole;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;

class EnrollmentSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        //DB::table('Enrollment')->truncate();

        $faker = Faker::create();
        $classeIds = Classe::pluck('id')->toArray();
        $userIds = UserRole::whereRelation('role','nameEn','student')->pluck('userId')->toArray();
        foreach ($classeIds as $classeId) {
            // create 20 students enrollment
            for ($i = 0; $i < 20; $i++) {
                Enrollment::create([
                    'id' => $faker->uuid,
                    'userId' => $faker->randomElement($userIds),
                    'classeId' => $classeId,
                ]);
            }
        }
    }
}
