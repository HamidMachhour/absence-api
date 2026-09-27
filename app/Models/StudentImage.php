<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use App\Models\AbsenceModel;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

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

	protected $fillable = [
		'studentId',
		'uri'
	];

	public function getUriAttribute($value)
	{
		if (!$value) {
			return $value;
		}

		if (filter_var($value, FILTER_VALIDATE_URL) || Str::startsWith($value, ['//', 'data:'])) {
			return $value;
		}

		return asset(ltrim($value, '/'));
	}

    public function student()
    {
        return $this->belongsTo(Student::class, 'studentId');
    }
}
