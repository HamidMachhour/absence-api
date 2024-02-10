<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Subscription
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string|null $planId
 * @property Carbon|null $expire_at
 * @property string|null $status
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Subscription extends AbsenceModel
{
    use SoftDeletes;

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
