<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class SubscriptionHistory
 *
 * @property string|null $id
 * @property string|null $subscriptionId
 * @property string|null $action
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class SubscriptionHistory extends AbsenceModel
{
    

	protected $table = 'SubscriptionHistory';
	protected $fillable = [
		'subscriptionId',
		'action'
	];
}
