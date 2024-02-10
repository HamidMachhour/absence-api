<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\SoftDeletes;
/**
 * Class Matiere
 *
 * @property string|null $id
 * @property string|null $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Matiere extends AbsenceModel
{
	protected $table = 'Matiere';
    use SoftDeletes;

	protected $fillable = [
		'name'
	];
}
