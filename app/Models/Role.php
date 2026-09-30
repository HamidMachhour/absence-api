<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = '_Role';

    protected $fillable = [
        'name',
        'nameFr',
        'nameEn',
        'nameZ'
    ];

    public function roleName()
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

    /**
     * Get the user roles associated with the role.
     */
    public function userRoles()
    {
        return $this->hasMany(UserRole::class, 'roleId');
    }

    protected static function booted()
    {
        parent::boot();

        static::deleting(function ($role) {
            DB::transaction(function () use ($role) {
                $role->userRoles()->delete();
            });
        });
    }
}
