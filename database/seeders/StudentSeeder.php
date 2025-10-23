<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Classe;
use App\Models\Student;
use App\Models\UserRole;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class StudentSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        //DB::table('Student')->truncate();

        $faker = Faker::create();
        $classeIds = Classe::pluck('id')->toArray();
        $userIds = UserRole::whereRelation('role','nameEn','student')->pluck('userId')->toArray();

        foreach ($classeIds as $classeId) {
            // create 20 students
            for ($i = 0; $i < 20; $i++) {
                Student::create([
                    'id' => $faker->uuid,
                    'num' => $i+1,
                    'userId' =>  $faker->randomElement($userIds),
                    'classeId' => $classeId,
                ]);
            }
        }
    }
}
