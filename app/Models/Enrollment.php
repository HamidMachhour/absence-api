<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
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
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Enrollment extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'Enrollment';


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

        $query->when($request->filled('manager'), function ($q) use ($request) {
            $q->whereHas(
                'classe',
                function ($classe) use ($request) {
                    $classe->where('userId', $request->manager);
                }
            );
        });
        
        // New: sorting
        $query->when($request->filled('sort'), function ($q) use ($request) {
            if ($request->sort === 'newest')
                $q->orderBy('created_at', 'desc');
            elseif ($request->sort === 'oldest')
                $q->orderBy('created_at', 'asc');
        });

        return $query;
    }


    public static function nbEnrollments($managerId)
    {
        $students = Enrollment::whereHas(
            'classe',
            function ($classe) use ($managerId) {
                $classe->where('userId', $managerId);
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

    public function isOwnedBy($user)
    {
        // Check if the related 'classe' exists before trying to access its properties
        return $this->classe && $this->classe->userId === $user->id;
    }
}
