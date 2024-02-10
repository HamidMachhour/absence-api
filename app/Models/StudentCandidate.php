<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Student
 *
 * @property string|null $id
 * @property int $num
 * @property string $deleted_at
 * @property string $userId
 * @property string|null $classeId
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class StudentCandidate extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'StudentCandidate';


    protected $fillable = [
        'userId',
        'classeId',
    ];
    public function scopeFilter($query, Request $request)
    {
        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });
        $query->when($request->filled('classeId'), function ($q) use ($request) {
            $q->where('classeId', $request->classeId);
        });
        $query->when($request->filled('userId'), function ($q) use ($request) {
            $q->where('userId', $request->userId);
        });

        $query->when($request->filled('manager'), function ($q) use ($request) {
            $q->whereHas(
                'classe',
                function ($classe) use($request) {
                    $classe->where('userId', $request->manager);
                }
            );
        });
        $query->latest();
    }
public static function nbCondidate($managerId){
    $students=StudentCandidate::whereHas(
        'classe',
        function ($classe) use($managerId) {
            $classe->where('userId',$managerId);
        }
    )->count();
    return $students;
}
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }
}
