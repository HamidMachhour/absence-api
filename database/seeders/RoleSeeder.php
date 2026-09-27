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
        // DB::table('_Role')->truncate();
        $roles = [
            ['name' => 'مدير', 'nameFr' => 'admin', 'nameEn' => 'admin', 'nameZ' => 'ⴰⵎⵙⵉⵔ'],
            ['name' => 'مسير', 'nameFr' => 'gestionnaire', 'nameEn' => 'manager', 'nameZ' => 'ⴰⵎⵙⵡⵓⵇⴻⴹ'],
            ['name' => 'استاد', 'nameFr' => 'professeur', 'nameEn' => 'teacher', 'nameZ' => 'ⴰⵙⴻⵍⵎⴰⴷ'],
            ['name' => 'تلميذ', 'nameFr' => 'etudiant', 'nameEn' => 'student', 'nameZ' => 'ⴰⵏⴻⵍⵎⴰⴷ'],
            //add guest role
            ['name' => 'ضيف', 'nameFr' => 'invité', 'nameEn' => 'guest', 'nameZ' => 'ⴰⵣⵓⵍ'],
            //add terminal role
            ['name' => 'محطة', 'nameFr' => 'terminal', 'nameEn' => 'terminal', 'nameZ' => 'ⵜⴰⵎⵎⴰⵣⵉⵜ'],
        ];
        // Create 4 users
        foreach ($roles as $rolename) {
            Role::create($rolename);
        }
    }
}
