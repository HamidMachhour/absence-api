<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class PasswordReset
 *
 * @property string|null $email
 * @property string|null $token
 * @property DateTime $created_at
 *
 * @package App\Models
 */
class PasswordReset extends Model
{
	protected $table = 'PasswordReset';
	public $incrementing = false;
	public $timestamps = false;

	protected $hidden = [
		'token'
	];

	protected $fillable = [
		'email',
		'token'
	];
}
