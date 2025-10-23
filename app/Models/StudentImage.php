<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use App\Models\AbsenceModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class StudentImage
 *
 * @property string|null $id
 * @property string|null $studentId
 * @property string|null $uri
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class StudentImage extends AbsenceModel
{
	protected $table = 'StudentImage';
    use SoftDeletes;

	protected $fillable = [
		'studentId',
		'uri'
	];

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
