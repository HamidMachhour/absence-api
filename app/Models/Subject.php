<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;

/**
 * Class Subject
 *
 * @property string|null $id
 * @property string|null $name
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Subject extends AbsenceModel
{
	protected $table = 'Subject';
	use SoftDeletes;

	protected $fillable = [
		'name',
		'nameFr',
		'nameEn',
		'nameZ'
	];

	public function subjectName()
	{
		$locale = Session::get('locale', 'en'); // Get the locale from the session, with 'en' as the default

		if ($locale == 'ar') {
			return $this->name;
		} elseif ($locale == 'fr') {
			return $this->nameFr;
		} elseif ($locale == 'en') {
			return $this->nameEn;
		}
		return $this->nameZ;
	}
}
