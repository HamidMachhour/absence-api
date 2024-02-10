<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class AbsenceStudent
 *
 * @property string|null $id
 * @property string|null $studentId
 * @property string|null $absenceId
 * @property float|null $plage
 * @property string|null $justification
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class AbsenceStudent extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'AbsenceStudent';
    protected $casts = [
        'plage' => 'float'
    ];

    protected $fillable = [
        'studentId',
        'absenceId',
        'plage',
        'justification'
    ];

    public function total()
    {
        return round($this->plage , 2);
    }

    public function student(){
        return $this->belongsTo(Student::class, 'studentId');
    }
}
