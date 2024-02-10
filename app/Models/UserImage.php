<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class UserImage
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string|null $uri
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class UserImage extends AbsenceModel
{
    use SoftDeletes;

	protected $table = 'UserImage';

	protected $fillable = [
		'userId',
		'uri'
	];
}
