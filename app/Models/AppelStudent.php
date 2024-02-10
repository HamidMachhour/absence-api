<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;

/**
 * Class AppelStudent
 *
 * @property string $id
 * @property string $appelId
 * @property string $studentId
 * @property string $seanceId
 * @property float|null $plage
 * @property string|null $justification
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class AppelStudent extends AbsenceModel
{
    protected $table = 'AppelStudent';
    protected $casts = [
        'plage' => 'float'
    ];
 
    protected $fillable = [
        'appelId',
        'studentId',
        'seanceId'
    ];

    public function appel()
    {
        return $this->belongsTo(Appel::class, 'appelId');
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seanceId');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
