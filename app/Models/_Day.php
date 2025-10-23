<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class _Day extends Model
{
    protected $table = '_Day';
    use HasFactory;

    protected $fillable = [
        'name',
        'nameFr',
        'nameEn',
        'nameZ',
    ];

    public function seances()
    {
        return $this->hasMany(Seance::class, 'dayId');
    }

    public function dayName()
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
