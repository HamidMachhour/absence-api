<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Illuminate\Support\Str;

use Exception;
use DateTime;

/**
 * Class Attendance
 *
 * @property string|null $id
 * @property string|null $userId
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Attendance extends AbsenceModel
{
    protected $table = 'Attendance';

    protected $fillable = [
        'seanceId'
    ];

    public function attendanceStudents()
    {
        return $this->hasMany(AttendanceStudent::class, 'attendanceId');
    }

    public function pushStudent($studentId, $plage)
    {
        // 1. Calculate and validate the attendance duration (plage)
        $seancePlage = $this->seance->plageInMinutes();
        $plage = max(0, min($plage, $seancePlage)); // Ensures plage is within a valid range [0, seance_plage]

        // 2. Find or create the AttendanceStudent record in a single operation
        try {
            AttendanceStudent::updateOrCreate(
                [
                    'attendanceId' => $this->id,
                    'studentId' => $studentId,
                ],
                [
                    'plage' => $plage
                ]
            );
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function popStudent($studentId)
    {
        try {
            // Find the specific attendance record for the student and delete it
            $deletedCount = AttendanceStudent::where('attendanceId', $this->id)
                ->where('studentId', $studentId)
                ->delete();

            // The delete() method returns the number of rows affected.
            // Return true if at least one row was deleted, otherwise false.
            return $deletedCount > 0;
        } catch (Exception $e) {
            // Return false on any database error
            return false;
        }
    }


    public function pushAllStudents($plage)
    {
        try {
            // 1. Calculate and validate the attendance duration (plage)
            $seancePlage = $this->seance->plageInMinutes();
            $plage = max(0, min($plage, $seancePlage));

            // 2. Prepare the data for all students in the class
            $students = $this->seance->classe->students;
            $dataToInsert = [];

            foreach ($students as $student) {
                $dataToInsert[] = [
                    'id' => (string) Str::uuid(), // Manually generate the UUID
                    'attendanceId' => $this->id,
                    'studentId' => $student->id,
                    'plage' => $plage,
                    'created_at' => now(), // Add timestamps
                    'updated_at' => now(),
                ];
            }

            // 3. Perform a single database operation using `upsert`
            AttendanceStudent::upsert($dataToInsert, ['attendanceId', 'studentId'], ['plage', 'created_at', 'updated_at']);

            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    public function popAllStudents()
    {
        try {
            // Delete the parent Attendance record
            // The 'deleting' event is triggered, and all related
            // AttendanceStudent records are deleted via a database cascade.
            $this->delete();

            return true;
        } catch (Exception $e) {
            return false;
        }
    }


    public function seance()
    {
        return $this->belongsTo(Seance::class, 'seanceId');
    }
}
