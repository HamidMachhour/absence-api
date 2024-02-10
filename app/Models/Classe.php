<?php

namespace App\Models;

use Illuminate\Support\Str;
use App\Models\AbsenceModel;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\SoftDeletes;

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
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class Classe extends AbsenceModel
{
    use SoftDeletes;

    protected $table = 'Classe';


    protected $fillable = [
        'name',
        'description',
        'tags',
        'userId',
        'imageId',
    ];
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
}
