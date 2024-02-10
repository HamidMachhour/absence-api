<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Role extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = '_Role';

    protected $fillable = [
        'name',
        'nameFr',
        'nameEn'
    ];


}
