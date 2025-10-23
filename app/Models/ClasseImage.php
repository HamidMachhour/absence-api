<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

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
    use SoftDeletes;

	protected $table = 'ClasseImage';

	protected $fillable = [
		'classeId',
		'uri'
	];


    public function classe()
    {
        return $this->belongsTo(Classe::class, 'classeId');
    }
}
