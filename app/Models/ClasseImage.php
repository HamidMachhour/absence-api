<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

/**
 * Class ClasseImage
 *
 * @property string|null $id
 * @property string|null $classeId
 * @property string|null $uri
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class ClasseImage extends AbsenceModel
{
	protected $table = 'ClasseImage';

	protected $fillable = [
		'classeId',
		'uri'
	];

	public function getUriAttribute($value)
	{
		if (!$value) {
			return $value;
		}

		if (filter_var($value, FILTER_VALIDATE_URL) || Str::startsWith($value, ['//', 'data:'])) {
			return $value;
		}

		return asset(ltrim($value, '/'));
	}


    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }
}
