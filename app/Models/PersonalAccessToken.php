<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PersonalAccessToken
 *
 * @property int|null $id
 * @property string|null $tokenable_type
 * @property int|null $tokenable_id
 * @property string|null $name
 * @property string|null $token
 * @property string $abilities
 * @property DateTime $last_used_at
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class PersonalAccessToken extends Model
{
	protected $table = 'personal_access_tokens';

	protected $casts = [
		'tokenable_id' => 'int',
		'last_used_at' => 'datetime'
	];

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'tokenable_type',
		'tokenable_id',
		'name',
		'token',
		'abilities',
		'last_used_at'
	];
}
