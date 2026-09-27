<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Subscription
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string|null $planId
 * @property DateTime|null $expire_at
 * @property string|null $status
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Subscription extends AbsenceModel
{
    

	protected $table = 'Subscription';
	protected $casts = [
		'expire_at' => 'datetime'
	];

	protected $fillable = [
		'userId',
		'planId',
		'expire_at',
		'status'
	];
}
