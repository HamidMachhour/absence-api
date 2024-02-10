<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Clear the users table
        DB::table('_Role')->truncate();
        $roles = [
            ['name' => 'مدير','nameFr' => 'admin','nameEn' => 'admin'],
            ['name' => 'مسير','nameFr' => 'gestionnaire','nameEn' => 'manager'],
            ['name' => 'استاد','nameFr' => 'professeur','nameEn' => 'teacher'],
            ['name' => 'تلميد','nameFr' => 'etudiant','nameEn' => 'student'],
        ];
        // Create 4 users
        foreach ($roles as $rolename) {
            Role::create($rolename);
        }
    }
}
