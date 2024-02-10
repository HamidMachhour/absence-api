<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Address;
use Faker\Factory as Faker;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AddressSeeder extends Seeder
{
    public function run()
    {
         // Clear  table
         DB::table('Address')->truncate();

        $faker = Faker::create();
        $userIds = User::pluck('id')->toArray();

        foreach($userIds as $userId){
            for ($i = 0; $i < 2; $i++) {
                $id=$faker->uuid;
                $address = new Address();
                $address->id=$id;
                $address->strAddress = $faker->streetAddress;
                $address->city = $faker->city;
                $address->zipCode = $faker->postcode;
                $address->country = $faker->country;
                $address->location = $faker->latitude . ',' . $faker->longitude;
                $address->phones = $faker->phoneNumber;
                $address->email = $faker->email;
                $address->userId = $userId;
                $address->save();
            }
        }

    }
}
