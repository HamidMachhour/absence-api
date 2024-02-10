<?php

namespace App\Models;

use App\Models\Classe;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;


/**
 * Class User
 *
 * @property string|null $id
 * @property string|null $firstName
 * @property string|null $lastName
 * @property string|null $code
 * @property string|null $email
 * @property Carbon $email_verified_at
 * @property string|null $password
 * @property string $remember_token
 * @property string $deleted_at
 * @property string $addressId
 * @property string $imageId
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @package App\Models
 */
class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;
    use SoftDeletes;
    public $incrementing = false;
    protected $keyType = 'uuid';

    protected $table = '_User';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'remember_token',
        'addressId',
        'imageId'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->id)) {
                $model->id = (string) Str::uuid();
            }
        });
    }

    public function scopeFilter($query, Request $request)
    {

        $query->when($request->filled('id'), function ($q) use ($request) {
            $q->where('id', $request->id);
        });

        $query->when($request->filled('role_id'), function ($q) use ($request) {
            $q->whereHas('roles', function ($roles) use ($request) {
                $roles->where('role_id', $request->role_id);
            });
        });

        $query->when($request->filled('verified'), function ($q) use ($request) {
            if ($request->verified === 'yes')
                $q->whereNotNull('email_verified_at');
            else
                $q->whereNull('email_verified_at');
        });

        $query->when($request->filled('search'), function ($q) use ($request) {
            $q->where('firstName', 'like', '%' . $request->search . '%')
                ->orWhere('lastName', 'like', '%' . $request->search . '%')
                ->orWhere('email', 'like', '%' . $request->search . '%')
                ->orWhere('code', 'like', '%' . $request->search . '%');
        });
    }

    public function appel()
    {
        $appels = Appel::where('userId', $this->id)->get();
        if ($appels->count() > 0)
            return $appels->first();

        return Appel::create(['userId' => $this->id]);
    }

    public function seances()
    {
        return $this->hasMany(Seance::class, 'profId');
    }

    public function absences()
    {
        return $this->hasMany(Absence::class, 'userId');
    }
    // Relationship With Classe
    public function classes()
    {
        return $this->hasMany(Classe::class, 'userId');
    }

    public function appels()
    {
        return $this->hasMany(Appel::class, 'userId');
    }
    public function students()
    {
        return $this->hasMany(Student::class, 'userId');
    }
    public function addresses()
    {
        return $this->hasMany(Address::class, 'userId');
    }


    public function address()
    {
        return $this->belongsTo(Address::class, 'addressId');
    }

    public function userImage()
    {
        return $this->belongsTo(UserImage::class, 'imageId');
    }

    public function userImages()
    {
        return $this->hasMany(UserImage::class, 'userId');
    }
    public function roles()
    {
        return $this->hasMany(UserRole::class, 'user_id');
    }


    public function isTeacher()
    {
        return ($this->hasRole('teacher') ||
            Seance::where('profId', $this->id)->exists());
    }

    public function isStudentCandidate()
    {
        $exists = Student::where('userId', $this->id)->exists();
        return ($this->hasRole('student') && !$exists);
    }
    public function isStudent()
    {
        return Student::where('userId', $this->id)->exists();
    }

    public function hasRole($role_name)
    {
        $exists = UserRole::where('user_id', $this->id)
            ->whereRelation('role', 'nameEn', $role_name)
            ->exists();
        /*
        $exists = UserRole::where('user_id', $this->id)
            ->whereHas(
                'role',
                function ($role) use ($role_name) {
                    $role->where('nameEn', $role_name);
                }
            )->exists();
            */
        return $exists;
    }

    public function nbCandidates()
    {
        return StudentCandidate::nbCondidate($this->id);
    }

    public static function withRole($role_name)
    {
        $users = User::whereHas(
            'roles',
            function ($roles) use ($role_name) {
                $roles->whereHas(
                    'role',
                    function ($role) use ($role_name) {
                        $role->where('nameEn', $role_name);
                    }
                );
            }
        );

        return $users;
    }
}
