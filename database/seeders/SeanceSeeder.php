<?php

namespace Database\Seeders;

use DateTime;
use DateInterval;
use App\Models\_Day;
use App\Models\User;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class SeanceSeeder extends Seeder
{
    /**
     * @param DateTime $start
     * @param DateTime $end
     * @return DateTime
     */
    function randomBetween(DateTime $start, DateTime $end)
    {
        // Get the timestamp of the start and end dates
        $startTimestamp = $start->getTimestamp();
        $endTimestamp = $end->getTimestamp();

        // Generate a random timestamp within the range
        $randomTimestamp = mt_rand($startTimestamp, $endTimestamp);

        // Create a new DateTime object from the random timestamp
        $randomDate = new DateTime();
        $randomDate->setTimestamp($randomTimestamp);

        return $randomDate;
    }

    public function run()
    {
        // Clear  table
        //DB::table('Seance')->truncate();
        // Get all user IDs from the User table
        $userIds = UserRole::whereRelation('role', 'nameEn', 'teacher')->pluck('userId')->toArray();

        $faker = FakerFactory::create();

        $subjectIds = Subject::pluck('id')->toArray();
        $classeIds = Classe::pluck('id')->toArray();
        $jours = _Day::pluck('id')->toArray();
        // Loop through user IDs and create 10 seances for each user
        foreach ($userIds as $userId) {

            // Generate 10 random seances
            for ($i = 1; $i <= 10; $i++) {
                $id = $faker->uuid();
                $startTime = new DateTime('08:00');
                $endTime = new DateTime('16:00');

                // Generate a random time between the start and end times
                $randomTime = $this->randomBetween($startTime, $endTime);

                DB::table('Seance')->insert([
                    'id' => $id,
                    'teacherId' => $userId,
                    'subjectId' => $faker->randomElement($subjectIds),
                    'classeId' => $faker->randomElement($classeIds),
                    'dayId' => $faker->randomElement($jours),
                    'salle' => rand(1, 20),
                    'de' => $randomTime->format('H:i'),
                    'a' => $randomTime->add(new DateInterval('PT' . rand(1, 2) . 'H'))->format('H:i'),
                ]);
            }
        }
    }
}
