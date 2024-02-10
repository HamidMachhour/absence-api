<?php

namespace Database\Seeders;

use App\Models\Jour;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class JourSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the users table
        DB::table('Jour')->truncate();
        $days = [
            ['name' => 'الأحد', 'nameFr' => 'Dimanche', 'nameEn' => 'Sunday'],
            ['name' => 'الإثنين', 'nameFr' => 'Lundi', 'nameEn' => 'Monday'],
            ['name' => 'الثلاثاء', 'nameFr' => 'Mardi', 'nameEn' => 'Tuesday'],
            ['name' => 'الأربعاء', 'nameFr' => 'Mercredi', 'nameEn' => 'Wednesday'],
            ['name' => 'الخميس', 'nameFr' => 'Jeudi', 'nameEn' => 'Thursday'],
            ['name' => 'الجمعة', 'nameFr' => 'Vendredi', 'nameEn' => 'Friday'],
            ['name' => 'السبت', 'nameFr' => 'Samedi', 'nameEn' => 'Saturday'],
        ];

        foreach ($days as $day) {
            Jour::create($day);
        }
    }
}
