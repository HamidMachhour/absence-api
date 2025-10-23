<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;

/**
 * Class AttendanceStudent
 *
 * @property string $id
 * @property string $attendanceId
 * @property string $studentId
 * @property string $seanceId
 * @property float|null $plage
 * @property string|null $justification
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class AttendanceStudent extends AbsenceModel
{
    protected $table = 'AttendanceStudent';
    protected $casts = [
        'plage' => 'float'
    ];
 
    protected $fillable = [
        'attendanceId',
        'studentId'
    ];

    public function attendance()
    {
        return $this->belongsTo(Attendance::class, 'attendanceId');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
