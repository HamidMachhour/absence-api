<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class SubscriptionHistory
 *
 * @property string|null $id
 * @property string|null $subscriptionId
 * @property string|null $action
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class SubscriptionHistory extends AbsenceModel
{
    use SoftDeletes;

	protected $table = 'SubscriptionHistory';
	protected $fillable = [
		'subscriptionId',
		'action'
	];
}
