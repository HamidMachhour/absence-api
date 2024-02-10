<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use App\Models\UserImage;
use App\Models\User;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class UserImageSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        DB::table('UserImage')->truncate();

        $faker = FakerFactory::create();

        // Get a list of user IDs
        $userData = User::pluck('firstName','id');

        // Create 4 user images for each user
        foreach ($userData as $userId=>$name) {
            for ($i = 1; $i <= 4; $i++) {
                $id=$faker->uuid;

                UserImage::create([
                    'id' => $id,
                    'userId' => $userId,
                    //'uri' => $faker->imageUrl(),
                    'uri' => customImageUrl(640, 480, $faker->hexColor, $name),
                ]);
            }
        }
    }
}
