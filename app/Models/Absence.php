<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class Absence
 *
 * @property string|null $id
 * @property string $seanceId
 * @property string $deleted_at
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Absence extends AbsenceModel
{

    protected $table = 'Absence';


    protected $fillable = [
        'seanceId',
    ];

        protected static function booted()
    {
        parent::boot();

        static::deleting(function ($absence) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($absence) {
                $absence->absenceStudents->each->delete();
            });
        });
    }

    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seanceId');
    }

    public function absenceStudents()
    {
        return $this->hasMany(AbsenceStudent::class, 'absenceId');
    }


    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('seanceId'), function ($q) use ($request) {
            $q->where('seanceId', $request->seanceId);
        });
        $query->when($request->filled('teacherId'), function ($q) use ($request) {
            $q->whereRelation('seance', 'teacherId',$request->teacherId);
        });

        $query->when($request->filled('classeId'), function ($q) use ($request) {
            $q->whereRelation('seance', 'classeId', $request->classeId);
        });

        $query->when($request->filled('subjectId'), function ($q) use ($request) {
            $q->whereRelation('seance', 'subjectId', $request->subjectId);
        });
        $query->when($request->filled('dayId'), function ($q) use ($request) {
            $q->whereRelation('seance', 'dayId', $request->dayId);
        });
        $query->when($request->filled('salle'), function ($q) use ($request) {
            $q->whereRelation('seance', 'salle', $request->salle);
        });

        $query->when($request->filled('date_absence'), function ($q) use ($request) {
            $q->where('created_at', '>=', $request->date_absence);
        });

        $query->when($request->filled('days'), function ($q) use ($request) {
            $q->where('created_at', '>', now()->subDays($request->days)->endOfDay());
        });


        return $query;
    }
}
