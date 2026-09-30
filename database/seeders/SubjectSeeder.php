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
        // Clear table (uncomment if needed)
        // DB::table('subjects')->truncate(); // Assuming your table is 'subjects'


        $subjects = [
            // Core Sciences and Languages (Existing)
            ['name' => 'الرياضيات', 'nameFr' => 'Mathématiques', 'nameEn' => 'Mathematics', 'nameZ' => 'ⵜⵉⵎⵙⵏⴰⵙⵉⵏ'],
            ['name' => 'اللغة العربية', 'nameFr' => 'Langue Arabe', 'nameEn' => 'Arabic Language', 'nameZ' => 'ⵜⴰⵄⵔⴰⴱⵜ'],
            ['name' => 'اللغة الفرنسية', 'nameFr' => 'Français', 'nameEn' => 'French Language', 'nameZ' => 'ⵜⴰⴼⵔⴰⵏⵙⵉⵙⵜ'],
            ['name' => 'العلوم الفيزيائية والكيميائية', 'nameFr' => 'Sciences Physiques et Chimiques', 'nameEn' => 'Physical and Chemical Sciences', 'nameZ' => 'ⵜⵉⵙⵏⴰⵏⴰⵢⵉⵏ ⵜⵉⵎⵥⵢⴰⵏⵉⵏ ⴷ ⵜⵉⴽⵉⵎⵢⴰⵏⵉⵏ'],
            ['name' => 'علوم الحياة والأرض', 'nameFr' => 'Sciences de la Vie et de la Terre', 'nameEn' => 'Life and Earth Sciences', 'nameZ' => 'ⵜⵉⵙⵏⴰⵏⴰⵢⵉⵏ ⵏ ⵜⵓⴷⵔⵜ ⴷ ⵡⴰⴽⴰⵍ'],

            // Religious and Social Studies
            ['name' => 'التربية الإسلامية', 'nameFr' => 'Éducation Islamique', 'nameEn' => 'Islamic Education', 'nameZ' => 'ⴰⵙⴳⵎⵓ ⴰⴷⵉⵏⵉ'],
            ['name' => 'الاجتماعيات', 'nameFr' => 'Sciences Sociales', 'nameEn' => 'Social Studies', 'nameZ' => 'ⵜⵉⵎⵙⴽⴰⵔ ⵜⵉⵎⵢⴰⵏⵉⵏ'],
            ['name' => 'التاريخ والجغرافيا', 'nameFr' => 'Histoire et Géographie', 'nameEn' => 'History and Geography', 'nameZ' => 'ⴰⵎⵣⵔⵓⵢ ⴷ ⵜⴰⵎⴰⵥⴰⵢⵜ'],
            ['name' => 'الفلسفة', 'nameFr' => 'Philosophie', 'nameEn' => 'Philosophy', 'nameZ' => 'ⵜⴰⴼⵍⵙⴼⵜ'],

            // Foreign Languages
            ['name' => 'اللغة الإنجليزية', 'nameFr' => 'Anglais', 'nameEn' => 'English Language', 'nameZ' => 'ⵜⴰⵏⴳⵍⵉⵣⵉⵜ'],
            ['name' => 'اللغة الإسبانية', 'nameFr' => 'Espagnol', 'nameEn' => 'Spanish Language', 'nameZ' => 'ⵜⴰⵙⴱⴰⵏⵢⵓⵜ'], // Taught in some schools/regions

            // Technical and Arts
            ['name' => 'التربية البدنية والرياضة', 'nameFr' => 'Éducation Physique et Sportive', 'nameEn' => 'Physical Education and Sport', 'nameZ' => 'ⴰⵙⴳⵎⵓ ⴰⴼⵉⵣⵉⴽⵉ ⴷ ⵜⴰⵏⵏⴰⵢⵜ'],
            ['name' => 'التربية التشكيلية', 'nameFr' => 'Arts Plastiques', 'nameEn' => 'Plastic Arts', 'nameZ' => 'ⵜⴰⵙⵏⴰ ⵜⴰⵎⵙⴽⴰⵍⵜ'],
            ['name' => 'الإعلاميات', 'nameFr' => 'Informatique', 'nameEn' => 'Computer Science', 'nameZ' => 'ⴰⵎⵙⴽⴰⵔ ⵏ ⵉⵏⴼⵓⵔⵎⴰⵜⵉⴽ'],
        ];

        foreach ($subjects as $subject) {
            // Check if the subject already exists to prevent duplicates on rerun
            if (!Subject::where('name', $subject['name'])->exists()) {
                $subject['id'] = Str::uuid();
                Subject::create($subject);
            }
        }
    }
}
