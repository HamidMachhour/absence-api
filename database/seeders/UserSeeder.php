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
        //DB::table('_User')->truncate();
 

        //create one admin
        $this->createUserWithRole(['name' => 'مدير', 'nameFr' => 'admin', 'nameEn' => 'admin', 'nameZ' => 'ⴰⵎⵙⵉⵔ']);
        //create 4 managers
        for ($i = 0; $i < 4; $i++) {
            $this->createUserWithRole(['name' => 'مسير', 'nameFr' => 'gestionnaire', 'nameEn' => 'manager', 'nameZ' => 'ⴰⵎⵙⵡⵓⵇⴻⴹ']);
        }
        // Create 10 teachers
        for ($i = 0; $i < 10; $i++) {
            $this->createUserWithRole(['name' => 'استاد', 'nameFr' => 'professeur', 'nameEn' => 'teacher', 'nameZ' => 'ⴰⵙⴻⵍⵎⴰⴷ']);
        }
         //create 100 students
         for ($i = 0; $i < 100; $i++) {
            $this->createUserWithRole(['name' => 'تلميذ', 'nameFr' => 'etudiant', 'nameEn' => 'student', 'nameZ' => 'ⴰⵏⴻⵍⵎⴰⴷ']);
        }
    }
    public function createUserWithRole($_role)
    {
        DB::transaction(function () use ($_role) {
            $role = Role::where('nameEn', $_role['nameEn'])->first();
            if (!$role) {
                $role =  Role::create($_role);
            }
            
            // Generate fake data using Faker library
            $faker = \Faker\Factory::create();
            $id = $faker->uuid;
            $firstName=$faker->firstName();
            $lastName=$faker->lastName();
            $user = new User([
                'id' => $id,
                'firstName' => $firstName,
                'lastName' => $lastName,
                'firstNameFr' =>$firstName,
                'lastNameFr' => $lastName,
                'firstNameEn' => $firstName,
                'lastNameEn' => $lastName,
                'firstNameZ' => $firstName,
                'lastNameZ' => $lastName,
                'code' => $faker->randomNumber(5),
                'password' => Hash::make('password'),
                'email' => $faker->email,
                /* 'deleted_at' => $faker->randomElement(
                        [null,$faker->dateTimeBetween('-1 year', 'now')]), */
                'tag_id' => $faker->randomNumber(5),
                'addressId' => null,
                'imageId' => null,
            ]);
            $user->save();

            UserRole::create(['userId' => $user->id, 'roleId' => $role->id]);
        });
    }
}
