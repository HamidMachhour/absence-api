<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class MisconductReport extends Model
{
    use HasFactory;
    protected $table = 'MisconductReport';
    use SoftDeletes;

    //fillable fields
    public $fillable = [
        'studentId',
        'seanceId',
        'reportDate',
        'otherActs',
        'otherPropositions',
        'status'
    ];

    protected static function booted()
    {
        static::deleting(function ($report) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($report) {
                $report->misconductActs()->detach();
                $report->proposedActions()->detach();
            });
        });
    }
    // the status value
    public const STATUS_DRAFT = 'draft';
    public const STATUS_SUBMITTED = 'submitted';
    public const STATUS_REVIEWED = 'reviewed';
    public const STATUS_ARCHIVED = 'archived';

    // student
    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }

    // seance
    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seanceId'); 
    }

    // misconduct ats
    public function misconductActs()
    {
        return $this->belongsToMany(MisconductAct::class, 'ReportMisconductAction', 'reportId', 'misconductActionId');
    }

    // proposed actions
    public function proposedActions()
    {
        return $this->belongsToMany(ProposedAction::class, 'ReportProposedAction', 'reportId', 'proposedActionId');
    }

}
