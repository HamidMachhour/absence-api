<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
class Student extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'Student';

    protected $casts = [
        'num' => 'int',
        'absences_sum_heure' => 'float'
    ];

    protected $fillable = [
        'num',
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

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->whereHas('user', function ($user) use ($request) {
                $user->where('firstName', 'like', '%' . $request->search . '%')
                    ->orWhere('lastName', 'like', '%' . $request->search . '%')
                    ->orWhere('email', 'like', '%' . $request->search . '%');
            });
            $q->orWhere('num', $request->search);
        });

        if ($request->filled('minPlage') && $request->filled('maxPlage')) {
            $maxPlage = floatval($request->maxPlage);
            $minPlage = floatval($request->minPlage);
            if ($minPlage > $maxPlage) {
                $t = $minPlage;
                $minPlage = $maxPlage;
                $maxPlage = $t;
            }
            $query->havingBetween(DB::raw('sum("AbsenceStudent".plage)'), [$minPlage, $maxPlage]);
        } elseif ($request->filled('minPlage')) {
            $query->having(DB::raw('sum("AbsenceStudent".plage)'), '>=', floatval($request->minPlage));
        } elseif ($request->filled('maxPlage')) {
            $query->having(DB::raw('sum("AbsenceStudent".plage)'), '<=', floatval($request->maxPlage));
        }


        if ($request->filled('minAbsences') && $request->filled('maxAbsences')) {
            $maxAbsences = floatval($request->maxAbsences);
            $minAbsences = floatval($request->minAbsences);
            if ($minAbsences > $maxAbsences) {
                $t = $minAbsences;
                $minAbsences = $maxAbsences;
                $maxAbsences = $t;
            }
            $query->havingBetween(DB::raw('count(studentId)'), [$minAbsences, $maxAbsences]);
        } elseif ($request->filled('minAbsences')) {
            $query->having(DB::raw('count(studentId)'), '>=', floatval($request->minAbsences));
        } elseif ($request->filled('maxAbsences')) {
            $query->having(DB::raw('count(studentId)'), '<=', floatval($request->maxAbsences));
        }

        if ($request->filled('orderBy')) {
            $query->orderByRaw($request->orderBy);
        } else {
            $query->orderBy('num');
        }
        //dd($query->toSql(), $query->getBindings());
    }
    public function setAbsencesSumPlageAttribute($value)
    {
        $this->attributes['absences_sum_plage'] = $value ?? 0.0;
    }
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }

    public function absences()
    {
        return $this->hasMany(AbsenceStudent::class, 'studentId');
    }

    public function appels()
    {
        return $this->hasMany(AppelStudent::class, 'studentId');
    }
}
