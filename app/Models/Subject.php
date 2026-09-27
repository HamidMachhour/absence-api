<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

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

	protected static function booted()
    {
        parent::boot();

        static::deleting(function ($subject) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($subject) {
                // This will trigger the 'deleting' event on each Seance model,
                // which in turn will cascade to Absences and AbsenceStudents.
                $subject->seances()->each(function ($seance) {
                    $seance->delete();
                });
            });
        });
    }

	public function seances()
    {
        return $this->hasMany(Seance::class, 'subjectId');
    }

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
