<?php

namespace Database\Seeders;

use App\Models\MisconductAct;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class MisconductActSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('MisconductAction')->truncate();
        $misconduct_acts = [
            [
                'name_ar' => 'عدم إحضار دفتر ومستلزمات المادة',
                'name_fr' => "Absence du cahier et des fournitures de la matière",
                'name_en' => "Failure to bring the notebook and subject supplies",
                'name_z' => 'ⵓⵔ ⴷⵓⵙⴰⵏ ⴰⴷⴼⵜⴰⵔ ⴷ ⵡⴰⵢⴷⴰⵜⵏ ⵏ ⵜⵎⴰⴷⴷⴰ'
            ],
            [
                'name_ar' => 'الامتناع عن كتابة الدروس والانتباه والمشاركة داخل القسم',
                'name_fr' => "Refus d'écrire les leçons, de faire attention et de participer en classe",
                'name_en' => "Refusal to write lessons, pay attention, and participate in class",
                'name_z' => 'ⴰⵣⵣⵉⵍ ⴼ ⵓⵜⴰⵔⴰ ⵏ ⵉⴷⴷⵔⵓⵙ, ⴰⵎⵢⵉⵡⵉⵏ ⴷ ⵓⵙⴷⴷⵓⵔ ⴳ ⵜⵖⵔⵉ'
            ],
            [
                'name_ar' => 'عدم القيام بالواجبات المنزلية',
                'name_fr' => "Non-exécution des devoirs à la maison",
                'name_en' => "Failure to perform homework assignments",
                'name_z' => 'ⵓⵔ ⴷⵓⵙⴰⵏ ⵉⵡⴰⵊⵉⴱⵏ ⵏ ⵡⴰⵅⵅⴰⵎ'
            ],
            [
                'name_ar' => 'الثرثرة والشغب وعرقلة السير العادي للدرس',
                'name_fr' => "Bavardage, chahut et perturbation du déroulement normal du cours",
                'name_en' => "Chatting, disruption, and hindering the normal flow of the lesson",
                'name_z' => 'ⴰⴳⴷⵓⵔ, ⴰⵙⵅⴰⵏⴷⵉⴷ ⴷ ⵓⵥⵥⴰⴹ ⵏ ⵓⵙⵡⵓⵔⵉ ⴰⵎⴰⵖⵓⵔ ⵏ ⵜⵖⵔⵉ'
            ],
            [
                'name_ar' => 'مناولة الهاتف النقال داخل القسم',
                'name_fr' => "Utilisation du téléphone portable en classe",
                'name_en' => "Handling/Using a mobile phone inside the classroom",
                'name_z' => 'ⴰⵙⵙⵏⴰⵡ ⵏ ⵓⵙⴰⵔⴰⴳ ⴰⴷⵓⵙⴰⵏ ⴳ ⵜⵖⵔⵉ'
            ],
            [
                'name_ar' => 'اصدار اصوات مزعجة',
                'name_fr' => "Émission de bruits dérangeants",
                'name_en' => "Making disturbing noises",
                'name_z' => 'ⴰⵙⵙⴽⵜⵉ ⵏ ⵉⵙⵉⵡⵍⵏ ⵉⴱⵓⵖⵍⵉⵏ'
            ],
            [
                'name_ar' => 'الغش أثناء الفرض المحروس',
                'name_fr' => "Triche pendant le devoir surveillé",
                'name_en' => "Cheating during the supervised test",
                'name_z' => 'ⴰⴳⵓⵏⴼⵓ ⴳ ⵉⵎⵜⵜⵉⵃⴰⵏ ⴰⵎⵃⵔⵓⵙ'
            ],
            [
                'name_ar' => 'التلفظ بكلمات نابية مخلة بالحياء',
                'name_fr' => "Proférer des paroles obscènes portant atteinte à la pudeur",
                'name_en' => "Uttering obscene words that violate modesty",
                'name_z' => 'ⴰⵙⵙⵉⵡⵍ ⵙ ⵉⵡⴰⵍⵏ ⵉⵅⵛⵏⵏ ⴷ ⵉⵜⵜⴼⵔⴰⵖⵏ ⴰⵎⵢⴰⵏ'
            ],
            [
                'name_ar' => 'السب والشتم',
                'name_fr' => "Insultes et injures",
                'name_en' => "Cursing and insulting (Verbal abuse)",
                'name_z' => 'ⴰⵙⵙⵏⴼⵓ ⴷ ⴰⵙⵙⴱⵉ ⵏ ⵉⵎⴷⴷⵓⴽⴽⴰⵍ'
            ],
            [
                'name_ar' => 'المشاجرة داخل القسم',
                'name_fr' => "Querelle/Bagarre en classe",
                'name_en' => "Brawling/Fighting inside the classroom",
                'name_z' => 'ⴰⵎⵏⵖⵉ ⴳ ⵜⵖⵔⵉ'
            ],
            [
                'name_ar' => 'التهديد',
                'name_fr' => "Menace",
                'name_en' => "Threatening",
                'name_z' => 'ⴰⴳⵔⴰⵣ'
            ],
            [
                'name_ar' => 'تحرش جنسي',
                'name_fr' => "Harcèlement sexuel",
                'name_en' => "Sexual harassment",
                'name_z' => 'ⴰⵙⵉⵙⴼⵏ ⴰⴳⵏⵙⴰⵏ'
            ],
        ];
   
        foreach ($misconduct_acts as $misconduct_act) {
            MisconductAct::create($misconduct_act);
        }
    }
}
