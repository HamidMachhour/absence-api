<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Define the list of seeders to run.
        // The order here should match the dependency order, 
        // regardless of external execution methods.
        $this->call([
            PlanSeeder::class,
            RoleSeeder::class,
            _DaySeeder::class,
            MisconductActSeeder::class,
            ProposedActionSeeder::class,
            UserSeeder::class,        // Needs to run before UserImageSeeder
            UserImageSeeder::class,   // Needs to run before ClasseSeeder (based on your foreign key error)
            AddressSeeder::class,
            
            SubjectSeeder::class,
            SubscriptionSeeder::class,
            
            ClasseSeeder::class,
            ClasseImageSeeder::class, // Assuming this depends on ClasseSeeder
            SeanceSeeder::class,

            EnrollmentSeeder::class,
            StudentSeeder::class,

            // Attendance and Absence related seeders
            AttendanceSeeder::class,
            AttendanceStudentSeeder::class,
            AbsenceSeeder::class,
            AbsenceStudentSeeder::class,
        ]);
    }
}
