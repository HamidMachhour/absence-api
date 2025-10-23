<?php

namespace Database\Seeders;

use App\Models\ProposedAction;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;


class ProposedActionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // DB::table('ProposedAction')->truncate();
        $proposed_actions = [
            [
                'name_ar' => 'توجيه توبيخ وانذار للتلميذ',
                'name_fr' => 'Adresser un blâme et un avertissement à l’élève',
                'name_en' => 'Issue a reprimand and a warning to the student',
                'name_z' => 'ⴰⵙⵏⴼⵍⵍⵉ ⵏ ⵓⴱⵍⴰⵎ ⴷ ⵓⵙⴷⴷⵓⵔ ⵉ ⵓⵏⵍⵎⴰⴷ'
            ],
            [
                'name_ar' => 'استدعاء ولي أمره و اخباره بالأفعال المذكورة أعلاه',
                'name_fr' => 'Convoquer son tuteur légal et l’informer des actes mentionnés ci-dessus',
                'name_en' => 'Summon his legal guardian and inform them of the acts mentioned above',
                'name_z' => 'ⴰⵙⵜⴰⵢ ⵏ ⵓⵎⵃⵔⵓⵙ ⵏⵏⵙ ⴷ ⵓⵙⵏⵍⵎⴰⴷ ⵙ ⵉⴼⵄⴰⵍⵏ ⵉⵎⵉⵏⴰⵔⵏ'
            ],
            [
                'name_ar' => 'التزام التلميذ وولي أمره بعدم تكرار هذه الأفعال',
                'name_fr' => 'Engagement de l’élève et de son tuteur à ne pas réitérer ces actes',
                'name_en' => 'Commitment from the student and their guardian not to repeat these actions',
                'name_z' => 'ⴰⵍⵣⴰⵎ ⵏ ⵓⵏⵍⵎⴰⴷ ⴷ ⵓⵎⵃⵔⵓⵙ ⵏⵏⵙ ⴰⴷ ⵓⵔ ⴷ ⵉⵜⵜⴰⵔⴰⵔ ⵉⴼⵄⴰⵍⵏ ⴰⴷ'
            ],
            // --- Added Proposed Actions ---
            [
                'name_ar' => 'الحرمان المؤقت من حصص دراسية محددة',
                'name_fr' => 'Exclusion temporaire de séances de cours spécifiques',
                'name_en' => 'Temporary exclusion from specific class sessions',
                'name_z' => 'ⴰⵃⵊⵓⵎ ⴰⵎⵓⴳⴰⵔ ⵏ ⵉⵙⵔⵉⵎⵏ ⵉⵖⵔⵉⵡⵏ ⵉⵎⵓⵙⵙⵏⵏ'
            ],
            [
                'name_ar' => 'توقيف مؤقت عن الدراسة لمدة محددة',
                'name_fr' => 'Suspension temporaire des cours pour une période déterminée',
                'name_en' => 'Temporary suspension from school for a specified period',
                'name_z' => 'ⴰⵙⵙⵔⵙ ⴰⵎⵓⴳⴰⵔ ⵏ ⵜⵖⵔⵉ ⵙ ⵢⵉⵡⵏ ⵓⵎⵓⵙⵙⵏ'
            ],
            [
                'name_ar' => 'تحويل التلميذ إلى مؤسسة تعليمية أخرى',
                'name_fr' => 'Transfert de l’élève vers un autre établissement scolaire',
                'name_en' => 'Transferring the student to another educational institution',
                'name_z' => 'ⴰⵙⵙⵉⵡⴹ ⵏ ⵓⵏⵍⵎⴰⴷ ⵖⵔ ⵢⵉⵡⵏ ⵓⵙⵖⵔⵉ ⵢⴰⴹⵏⵉⵏ'
            ],
            [
                'name_ar' => 'عرض التلميذ على مجلس القسم لاتخاذ قرار تأديبي',
                'name_fr' => 'Présentation de l’élève au conseil de classe pour décision disciplinaire',
                'name_en' => 'Referring the student to the class council for a disciplinary decision',
                'name_z' => 'ⴰⵙⵙⵉⵡⴹ ⵏ ⵓⵏⵍⵎⴰⴷ ⵖⵔ ⵓⵙⵇⵇⵉⵎ ⵏ ⵜⵖⵔⵉ ⵃⵎⴰ ⴰⴷ ⵢⴰⵡⵉ ⴰⵖⵜⴰⵙ ⴰⴷⵉⴱⵉ'
            ],
        ];
   
        foreach ($proposed_actions as $proposed_action) {
            ProposedAction::create($proposed_action);
        }
    }
}
