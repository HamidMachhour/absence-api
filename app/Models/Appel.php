<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Exception;
use Carbon\Carbon;

/**
 * Class Appel
 *
 * @property string|null $id
 * @property string|null $userId
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Appel extends AbsenceModel
{
    protected $table = 'Appel';

    protected $fillable = [
        'userId'
    ];

    public function appelStudents()
    {
        return $this->hasMany(AppelStudent::class, 'appelId');
    }

    public function pushStudent(Seance $seance, $studentId, $plage, $date)
    {
        try {
            $appelStudent = AppelStudent::where('appelId', $this->id)
                ->where('studentId', $studentId)
                ->where('seanceId', $seance->id)
                ->whereDate('updated_at', $date)
                ->first();

            $seance_plage = $seance->plageInMinutes();
            $plage = ($plage <= 0 || $plage > $seance_plage) ? $seance_plage : $plage;

            if (!$appelStudent)
                $appelStudent = AppelStudent::create([
                    'appelId' => $this->id,
                    'seanceId' => $seance->id,
                    'studentId' => $studentId,
                    'plage' => $plage,
                ]);
            else {
                $appelStudent->update(['plage' => $plage]);
            }
            return true;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }

    public function pushAllStudents(Seance $seance, $plage, $date)
    {
        try {
            foreach ($seance->classe->students as $student) {
                $appelStudent = AppelStudent::where('appelId', $this->id)
                    ->where('studentId', $student->id)
                    ->where('seanceId', $seance->id)
                    ->whereDate('updated_at', $date)
                    ->first();

                $seance_plage = $seance->plageInMinutes();
                $plage = ($plage <= 0 || $plage > $seance_plage) ? $seance_plage : $plage;

                if (!$appelStudent)
                    $appelStudent = AppelStudent::create([
                        'appelId' => $this->id,
                        'seanceId' => $seance->id,
                        'studentId' => $student->id,
                        'plage' => $plage,
                    ]);
                else {
                    $appelStudent->update(['plage' => $plage]);
                }
            }
            return true;
        } catch (Exception $e) {
            dd($e);
            return false;
        }
    }
}
