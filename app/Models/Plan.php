<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Plan
 *
 * @property string|null $id
 * @property string|null $name
 * @property string|null $description
 * @property float|null $fee
 * @property int|null $max_classes
 * @property int|null $max_students
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Plan extends AbsenceModel
{
	protected $table = 'Plan';
    

	protected $casts = [
		'fee' => 'float',
		'max_classes' => 'int',
		'max_students' => 'int'
	];

	protected $fillable = [
		'name',
		'description',
		'fee',
		'max_classes',
		'max_students'
	];
}
