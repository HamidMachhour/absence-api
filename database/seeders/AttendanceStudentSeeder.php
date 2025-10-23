<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\AttendanceStudent;
use Faker\Factory as Faker;
use App\Models\Attendance;
use App\Models\Seance;
use App\Models\Student;
use Illuminate\Support\Facades\DB;

class AttendanceStudentSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        //DB::table('AttendanceStudent')->truncate();
        $faker = Faker::create();

        // Get all attendances and students
        $attendances = Attendance::all();
        $seances=Seance::all();
        foreach ($attendances as $attendance) {
            // Add random number of students to each attendance
            $numStudents = rand(1, 5);
            for ($i = 0; $i < $numStudents; $i++) {
                $id=$faker->uuid();
                $seance=$seances->random();
                $student=Student::where('classeId',$seance->classeId)->get()->random();
                $attendanceStudent = new AttendanceStudent([
                    'id'=>$id,
                    'attendanceId' => $attendance->id,
                    'studentId' => $student->id,
                    'seanceId' =>$seance->id,
                    'plage' => $faker->randomFloat(2, 0, $seance->plageInMinutes()),
                    'justification'=> $faker->sentence(3)
                ]);
                $attendanceStudent->save();
            }
        }
    }
}

