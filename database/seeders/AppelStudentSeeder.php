<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AppelStudent;
use Faker\Factory as Faker;
use App\Models\Appel;
use App\Models\Seance;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class AppelStudentSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        DB::table('AppelStudent')->truncate();
        $faker = Faker::create();

        // Get all appels and students
        $appels = Appel::all();
        $seances=Seance::all();
        foreach ($appels as $appel) {
            // Add random number of students to each appel
            $numStudents = rand(1, 5);
            for ($i = 0; $i < $numStudents; $i++) {
                $id=$faker->uuid();
                $seance=$seances->random();
                $student=Student::where('classeId',$seance->classeId)->get()->random();
                $appelStudent = new AppelStudent([
                    'id'=>$id,
                    'appelId' => $appel->id,
                    'studentId' => $student->id,
                    'seanceId' =>$seance->id,
                    'plage' => $faker->randomFloat(2, 0, $seance->plageInMinutes()),
                    'justification'=> $faker->sentence(3)
                ]);
                $appelStudent->save();
            }
        }
    }
}

