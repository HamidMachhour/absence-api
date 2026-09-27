<?php

namespace App\Models;

use App\Models\AbsenceModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

/**
 * Class Classe
 *
 * @property string|null $id
 * @property string|null $name
 * @property string $description
 * @property string $deleted_at
 * @property string|null $userId
 * @property string $imageId
 * @property string $tags
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Classe extends AbsenceModel
{
    

    protected $table = 'Classe';


    protected $fillable = [
        'name',
        'nameFr',
        'nameEn',
        'nameZ',
        'description',
        'tags',
        'userId',
        'imageId',
    ];

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($classe) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($classe) {
                // Use the collection property `$classe->students` to use `each->delete()`
                // This will trigger the 'deleting' event on each Student model.
                $classe->students->each->delete();
                // This will trigger the 'deleting' event on each Seance model.
                $classe->seances->each->delete();

                $classe->classeImages()->delete();
                $classe->enrollments()->delete();
            });
        });
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('userId'), function ($q) use ($request) {
            $q->where('userId', $request->userId);
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('name', 'like', '%' . $request->search . '%')
            ->orWhere('description', 'like', '%' . $request->search . '%')
            ->orWhere('tags', 'like', '%' . $request->search . '%');
        });

        $query->when($request->filled('tag'), function ($q) use ($request) {
            $q->where('tags', 'like', '%' . $request->tag . '%');
        });
        return $query;
    }

    // Relationship To User
    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }

    public function classeImage()
    {
        return $this->belongsTo(ClasseImage::class, 'imageId');
    }

    public function students()
    {
        return $this->hasMany(Student::class, 'classeId');
    }
    public function seances()
    {
        return $this->hasMany(Seance::class, 'classeId');
    }
    public function classeImages()
    {
        return $this->hasMany(ClasseImage::class, 'classeId');
    }

    public function enrollments()
    {
        return $this->hasMany(Enrollment::class, 'classeId');
    }

    public function classeName()
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
