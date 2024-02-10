<?php

namespace Database\Seeders;
use Illuminate\Database\Seeder;
use App\Models\Matiere;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class MatiereSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        DB::table('Matiere')->truncate();

        $methods = [
            ['name' => 'الرياضيات'],
            ['name' => 'العربية'],
            ['name' => 'الفرنسية'],
            ['name' => 'العلوم الفيزيائية'],
            ['name' => 'علوم الحياة و الارض'],
        ];

        foreach ($methods as $method) {
            $method['id'] = Str::uuid();
            Matiere::create($method);
        }
    }
}
