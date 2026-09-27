<?php

namespace Database\Seeders;

use App\Models\_Day;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class _DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $days = [
            ['name' => 'الأحد', 'nameFr' => 'Dimanche', 'nameEn' => 'Sunday', 'nameZ' => 'ⴰⵙⵙⴰⵎⴰⵙ'],
            ['name' => 'الإثنين', 'nameFr' => 'Lundi', 'nameEn' => 'Monday', 'nameZ' => 'ⴰⵙⵙⵉⵏⴰⵙ'],
            ['name' => 'الثلاثاء', 'nameFr' => 'Mardi', 'nameEn' => 'Tuesday', 'nameZ' => 'ⴰⵙⵙⵉⵏⴰⵙ'],
            ['name' => 'الأربعاء', 'nameFr' => 'Mercredi', 'nameEn' => 'Wednesday', 'nameZ' => 'ⴰⴽⵕⴰⵙ'],
            ['name' => 'الخميس', 'nameFr' => 'Jeudi', 'nameEn' => 'Thursday', 'nameZ' => 'ⴰⵙⵙⵉⵎⵡⴰⵙ'],
            ['name' => 'الجمعة', 'nameFr' => 'Vendredi', 'nameEn' => 'Friday', 'nameZ' => 'ⴰⵙⵙⵉⴷⵙ'],
            ['name' => 'السبت', 'nameFr' => 'Samedi', 'nameEn' => 'Saturday', 'nameZ' => 'ⴰⵙⵙⴰⴹ'],
        ];

        foreach ($days as $day) {
            $dayRecord = _Day::withTrashed()->updateOrCreate(
                ['nameEn' => $day['nameEn']],
                $day
            );

            if ($dayRecord->trashed()) {
                $dayRecord->restore();
            }
        }
    }
}
