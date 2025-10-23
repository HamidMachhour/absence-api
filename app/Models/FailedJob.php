<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\Model;

/**
 * Class FailedJob
 *
 * @property int|null $id
 * @property string|null $uuid
 * @property string|null $connection
 * @property string|null $queue
 * @property string|null $payload
 * @property string|null $exception
 * @property DateTime|null $failed_at
 *
 * @package App\Models
 */
class FailedJob extends Model
{
	protected $table = 'failed_jobs';
	public $timestamps = false;

	protected $casts = [
		'failed_at' => 'datetime'
	];

	protected $fillable = [
		'uuid',
		'connection',
		'queue',
		'payload',
		'exception',
		'failed_at'
	];
}
