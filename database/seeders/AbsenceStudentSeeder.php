<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\AbsenceStudent;
use App\Models\Absence;
use App\Models\Student;
use Illuminate\Support\Facades\DB;
use Faker\Factory as Faker;


class AbsenceStudentSeeder extends Seeder
{
    public function run()
    {

        // Clear  table
        DB::table('AbsenceStudent')->truncate();
        $faker = Faker::create();

        // Get all absences and students
        $absences = Absence::all();

        foreach($absences as $absence){
              // Add random number of students to each absence
              $numItems = rand(1, 5);
              for ($i = 0; $i < $numItems; $i++) {
                $id=$faker->uuid();
                $student=Student::inRandomOrder()->first();
                $absence_student=new AbsenceStudent(
                    [
                        'id' =>$id,
                        'studentId' => $student->id,
                        'absenceId' => $absence->id,
                        'plage' => $faker->randomFloat(2, 1, 10),
                        'justification' => $faker->sentence(),
                    ]

                    );
                    $absence_student->save();
              }
        }
    }
}
