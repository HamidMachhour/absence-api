<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class UserImage
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string|null $uri
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class UserImage extends AbsenceModel
{
<<<<<<< HEAD
    

=======
>>>>>>> 7cfde7274412376bfe0117da5f7625213cc6378a
	protected $table = 'UserImage';

	protected $fillable = [
		'userId',
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
}
