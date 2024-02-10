<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use App\Models\UserRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the users table
        DB::table('_User')->truncate();


        //create one admin
        $this->createUserWithRole(['name' => 'مدير','nameFr' => 'admin','nameEn' => 'admin']);
        //create 4 managers
        for ($i = 0; $i < 4; $i++) {
            $this->createUserWithRole(['name' => 'مسير','nameFr' => 'gestionnaire','nameEn' => 'manager']);
        }
        // Create 10 teachers
        for ($i = 0; $i < 10; $i++) {
            $this->createUserWithRole(['name' => 'استاد','nameFr' => 'professeur','nameEn' => 'teacher']);
        }
         //create 100 students
         for ($i = 0; $i < 100; $i++) {
            $this->createUserWithRole(['name' => 'تلميد','nameFr' => 'etudiant','nameEn' => 'student']);
        }
    }
    public function createUserWithRole($_role)
    {
        DB::transaction(function () use ($_role) {
            $role = Role::where('name', $_role['name'])->latest()->first();
            if (!$role) {
                $role = new Role($_role);
            }
            $role->save();
            // Generate fake data using Faker library
            $faker = \Faker\Factory::create();
            $id = $faker->uuid;
            $user = new User([
                'id' => $id,
                'firstName' => $faker->firstName(),
                'lastName' => $faker->lastName(),
                'code' => $faker->randomNumber(5),
                'password' => Hash::make('password'),
                'email' => $faker->email,
                /* 'deleted_at' => $faker->randomElement(
                        [null,$faker->dateTimeBetween('-1 year', 'now')]), */
                'addressId' => null,
                'imageId' => null,
            ]);
            $user->save();

            UserRole::create(['user_id' => $user->id, 'role_id' => $role->id]);
        });
    }
}
