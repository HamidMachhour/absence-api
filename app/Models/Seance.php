<?php

namespace App\Models;

use DateTime;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Class Seance
 *
 * @property string|null $id
 * @property string $teacherId
 * @property string $subjectId
 * @property string $classeId
 * @property string $dayId
 * @property string|null $salle
 * @property DateTime $de
 * @property DateTime $a
 * @property DateTime $deleted_at
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */

class Seance extends AbsenceModel
{
    
    protected $table = 'Seance';


    protected $fillable = [
        'teacherId',
        'subjectId',
        'classeId',
        'dayId',
        'de',
        'a',
    ];


    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($seance) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($seance) {
                $seance->absences->each->delete(); // This will trigger cascading deletes on AbsenceStudent
                $seance->attendances->each->delete();
                $seance->reports->each->delete();
            });
        });
    }

 
    public function plageInMinutes()
    {
        // Create DateTime objects directly from the string values of $this->de and $this->a
        $start = new DateTime($this->de);
        $end = new DateTime($this->a);

        // Calculate the difference between the two dates
        $interval = $start->diff($end);

        // Get the total minutes
        $totalMinutes = ($interval->h * 60) + $interval->i;

        return $totalMinutes;
    }
    public function title()
    {
        $title=$this->day->dayName() .
                    ' ( ' .
                    (new DateTime($this->de))->format('H:i') .
                    ' - ' .
                    (new DateTime($this->a))->format('H:i') .
                    ' ) ';
        return $title;
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'seanceId');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'seanceId');
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('teacherId'), function ($q) use ($request) {
            $q->where('teacherId', $request->teacherId);
        });

        $query->when($request->filled('classeId'), function ($q) use ($request) {
            $q->where('classeId', $request->classeId);
        });

        $query->when($request->filled('subjectId'), function ($q) use ($request) {
            $q->where('subjectId', $request->subjectId);
        });


        $query->when($request->filled('dayId'), function ($q) use ($request) {
            $q->where('dayId', $request->dayId);
        });

        $query->when($request->filled('salle'), function ($q) use ($request) {
            $q->where('salle', $request->salle);
        });

        $query->when($request->filled('de'), function ($q) use ($request) {
            $q->where('de', '>=', $request->de);
        });

        $query->when($request->filled('a'), function ($q) use ($request) {
            $q->where('a', '<=', $request->a);
        });

        // New: sorting
        $query->when($request->filled('sort'), function ($q) use ($request) {
            if ($request->sort === 'day_asc')
                $q->orderBy('dayId', 'asc');
            elseif ($request->sort === 'day_desc')
                $q->orderBy('dayId', 'desc');
            elseif ($request->sort === 'from_asc')
                $q->orderBy('de', 'asc');
            elseif ($request->sort === 'to_asc')
                $q->orderBy('a', 'asc');
            elseif ($request->sort === 'newest')
                $q->orderBy('created_at', 'desc');
            elseif ($request->sort === 'oldest')
                $q->orderBy('created_at', 'asc');
        });

        return $query;
    }

    public function day()
    {
        return $this->belongsTo(_Day::class, 'dayId');
    }



    public function plage()
    {
        $start = new DateTime($this->de);
        $end = new DateTime($this->a);

        // Calculate the difference between the two dates
        $interval = $start->diff($end);

        // Get the total minutes
        $totalMinutes = ($interval->h * 60) + $interval->i;
        return $totalMinutes;
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class, 'subjectId');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacherId');
    }

    public function reports()
    {
        return $this->hasMany(MisconductReport::class, 'seanceId');
    }
}
