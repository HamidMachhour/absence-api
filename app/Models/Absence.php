<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Absence
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string $matiereId
 * @property string $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Absence extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'Absence';


    protected $fillable = [
        'userId',
        'seanceId',
    ];


    public function absenceStudents()
    {
        return $this->hasMany(AbsenceStudent::class, 'absenceId');
    }

    public function scopeFilter($query, array $filters)
    {
        if ($filters['id'] ?? false) {
            $query->where('id', request('id'));
        }

        if ($filters['userId'] ?? false) {
            $query->where('userId', request('userId'));
        }


        if ($filters['seanceId'] ?? false) {
            $query->where('seanceId', request('seanceId'));
        }

    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seanceId');
    }

}
