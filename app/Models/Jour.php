<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jour extends Model
{
    protected $table = 'Jour';
    use HasFactory;

    protected $fillable = [
        'name'
    ];

    public function seances()
    {
        return $this->hasMany(Seance::class, 'jourId');
    }
}
