<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\UserRole;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use App\Models\StudentCandidate;
use Illuminate\Support\Facades\DB;

class StudentCandidateSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        DB::table('StudentCandidate')->truncate();

        $faker = Faker::create();
        $classeIds = Classe::pluck('id')->toArray();
        $userIds = UserRole::whereRelation('role','nameEn','student')->pluck('user_id')->toArray();
        foreach ($classeIds as $classeId) {
            // create 20 students candidate
            for ($i = 0; $i < 20; $i++) {
                StudentCandidate::create([
                    'id' => $faker->uuid,
                    'userId' => $faker->randomElement($userIds),
                    'classeId' => $classeId,
                ]);
            }
        }
    }
}
