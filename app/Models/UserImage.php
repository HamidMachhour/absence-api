<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    

	protected $table = 'UserImage';

	protected $fillable = [
		'userId',
		'uri'
	];
}
