<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class UserRole extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'UserRole';

    protected $fillable = [
        'userId',
        'roleId'
    ];

    public function role()
    {
        return $this->belongsTo(Role::class, 'roleId');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'userId');
    }
    public function roleName()
    {
        return $this->role->roleName();
    }
}
