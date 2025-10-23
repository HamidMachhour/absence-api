<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClasseImage;
use App\Models\Classe;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\DB;

class ClasseImageSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        //DB::table('ClasseImage')->truncate();

        $faker = FakerFactory::create();

        // Get a list of store IDs
        $classes = Classe::all();
        // Create 4 store images for each store
        foreach ($classes as $classe) {
            for ($i = 1; $i <= 4; $i++) {
                $id = $faker->uuid;

                $image = ClasseImage::create([
                    'id' => $id,
                    'classeId' => $classe->id,
                    //'uri' => $faker->imageUrl(),
                    'uri' => customImageUrl(640, 480, $faker->hexColor, $classe->classeName()),
                ]);
                if (!$classe->imageId)
                    $classe->update(['imageId' => $image->id]);
            }
        }
    }
}
