<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Subject;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class SubjectSeeder extends Seeder
{
    public function run()
    {
        // Clear  table
        // DB::table('Subject')->truncate();


        $subjects = [
            ['name' => 'الرياضيات', 'nameFr' => 'Math', 'nameEn' => 'Math', 'nameZ' => 'ⵜⵉⵎⵙⵏⴰⵙⵉⵏ'],
            ['name' => 'العربية', 'nameFr' => 'Langue Arabe', 'nameEn' => 'Arabic', 'nameZ' => 'ⵜⴰⵄⵔⴰⴱⵜ'],
            ['name' => 'الفرنسية', 'nameFr' => 'Français', 'nameEn' => 'French', 'nameZ' => 'ⵜⴰⴼⵔⴰⵏⵙⵉⵙⵜ'],
            ['name' => 'العلوم الفيزيائية', 'nameFr' => 'PC', 'nameEn' => 'PS', 'nameZ' => 'ⵜⵉⵙⵏⴰⵏⴰⵢⵉⵏ ⵜⵉⵎⵥⵢⴰⵏⵉⵏ'],
            ['name' => 'علوم الحياة و الارض', 'nameFr' => 'SVT', 'nameEn' => 'LES', 'nameZ' => 'ⵜⵉⵙⵏⴰⵏⴰⵢⵉⵏ ⵏ ⵜⵓⴷⵔⵜ ⴷ ⵡⴰⴽⴰⵍ'],
        ];
        foreach ($subjects as $subject) {
            $subject['id'] = Str::uuid();
            Subject::create($subject);
        }
    }
}
