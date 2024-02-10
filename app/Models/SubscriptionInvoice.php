<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class SubscriptionInvoice
 *
 * @property string|null $id
 * @property string|null $userId
 * @property string|null $subscriptionId
 * @property float|null $amount
 * @property string|null $status
 * @property Carbon|null $due_date
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class SubscriptionInvoice extends AbsenceModel
{
    use SoftDeletes;

	protected $table = 'SubscriptionInvoice';
	protected $casts = [
		'amount' => 'float',
		'due_date' => 'datetime'
	];

	protected $fillable = [
		'userId',
		'subscriptionId',
		'amount',
		'status',
		'due_date'
	];
}
