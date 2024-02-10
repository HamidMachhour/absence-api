<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;


/**
 * Class Seance
 *
 * @property string|null $id
 * @property string $profId
 * @property string $matiereId
 * @property string $classeId
 * @property string $jourId
 * @property string|null $salle
 * @property Carbon $de
 * @property Carbon $a
 * @property Carbon $deleted_at
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */

class Seance extends AbsenceModel
{
    use SoftDeletes;
    protected $table = 'Seance';


    protected $fillable = [
        'profId',
        'matiereId',
        'classeId',
        'jourId',
        'de',
        'a',
    ];

    public function plageInMinutes()
    {
        return Carbon::parse($this->a)->diffInMinutes(Carbon::parse($this->de));
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

        $query->when($request->filled('profId'), function ($q) use ($request) {
            $q->where('profId', $request->profId);
        });

        $query->when($request->filled('classeId'), function ($q) use ($request) {
            $q->where('classeId', $request->classeId);
        });

        $query->when($request->filled('matiereId'), function ($q) use ($request) {
            $q->where('matiereId', $request->matiereId);
        });


        $query->when($request->filled('jourId'), function ($q) use ($request) {
            $q->where('jourId', $request->jourId);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('salle', $request->search);
        });
    }

    public function jour()
    {
        return $this->belongsTo(Jour::class, 'jourId');
    }

    public function plage()
    {
        $a=Carbon::parse($this->a)->setDate(1, 1, 1);
        $de=Carbon::parse($this->de)->setDate(1, 1, 1);
        return $a->diffInMinutes($de) ;
    }

    public function matiere()
    {
        return $this->belongsTo(Matiere::class, 'matiereId');
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }

    public function professeur()
    {
        return $this->belongsTo(User::class, 'profId');
    }
}
