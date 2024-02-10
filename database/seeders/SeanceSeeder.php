<?php

namespace Database\Seeders;

use Carbon\Carbon;
use App\Models\Jour;
use App\Models\User;
use App\Models\Classe;
use App\Models\Matiere;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class SeanceSeeder extends Seeder
{
    function randomBetween(Carbon $start, Carbon $end) {
        $randomTimestamp = mt_rand($start->timestamp, $end->timestamp);
        return Carbon::createFromTimestamp($randomTimestamp);
    }

    public function run()
    {
        // Clear  table
        DB::table('Seance')->truncate();
        // Get all user IDs from the User table
        $userIds = UserRole::whereRelation('role','nameEn','teacher')->pluck('user_id')->toArray();

        $faker = FakerFactory::create();

        $matiereIds = Matiere::pluck('id')->toArray();
        $classeIds = Classe::pluck('id')->toArray();
        $jours = Jour::pluck('id')->toArray();
        // Loop through user IDs and create 10 seances for each user
        foreach ($userIds as $userId) {

            // Generate 10 random seances
            for ($i = 1; $i <= 10; $i++) {
                $id = $faker->uuid();
                $startTime = Carbon::parse('08:00');
                $endTime = Carbon::parse('16:00');
                $randomTime = $this->randomBetween($startTime, $endTime);

                DB::table('Seance')->insert([
                    'id' => $id,
                    'profId' => $userId,
                    'matiereId' => $faker->randomElement($matiereIds),
                    'classeId' => $faker->randomElement($classeIds),
                    'jourId' => $faker->randomElement($jours),
                    'salle' => rand(0, 50),
                    'de' => $randomTime,
                    'a' => $randomTime->copy()->addHours(rand(1, 2)),
                ]);
            }
        }
    }
}
