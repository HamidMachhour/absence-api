<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Classe;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Models\Address;
use App\Models\StoreCategory;
use App\Models\Subscription;
use App\Models\UserRole;
use Faker\Factory as FakerFactory;
 
class ClasseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()

    {
        // Clear  table
        //DB::table('Classe')->truncate();

        $faker = FakerFactory::create();

        $userIds = UserRole::whereRelation('role','nameEn','manager')->pluck('userId')->toArray();

        foreach ($userIds as $userId) {
            $subscriptionIds = Subscription::where('userId', $userId)->pluck('id')->toArray();
            $tags = [];
            // Generate 6 fake tags
            for ($i = 0; $i < 4; $i++)
                $tags[] = $faker->word;
            $name=$faker->company;
            for ($i = 0; $i < 4; $i++) {
                Classe::create([
                    'id' => $faker->uuid,
                    'name' => $name,
                    'nameFr' => $name,
                    'nameEn' => $name,
                    'nameZ' => $name,
                    'description' => $faker->text,
                    'tags' =>  implode(',', $tags),
/*                     'deleted_at' => $faker->randomElement(
                        [null, $faker->dateTimeBetween('+1 month', '+6 months')]), */
                    'userId' => $userId,
                    'imageId' => null,
                    'subscriptionId' => $faker->randomElement($subscriptionIds),
                ]);
            }
        }
    }
}
