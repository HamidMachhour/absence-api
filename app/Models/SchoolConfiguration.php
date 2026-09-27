<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Session;

class SchoolConfiguration extends Model
{
    use HasFactory;
    protected $table = 'school_configurations';

    // Use guarded to allow all fields to be fillable, except for 'id', 
    // which is appropriate for a configuration model.
    protected $guarded = ['id'];

    // Cast date fields for automatic Carbon instance conversion
    protected $casts = [
        'current_academic_start' => 'date',
        'current_academic_end' => 'date',
    ];

    protected $fillable = [
        'name',
        'code',
        'city',
        'message',
        'address',
        'director_name',
        'phone_number',
        'email',
        'process_attendances_enabled',
    ];
    /**
     * Retrieves the single configuration row. 
     * @return SchoolConfiguration|null
     */

    public static function getSettings()
    {
        // Typically, configuration tables have only one row (ID 1). 
        // We use first() as a fallback just in case the row isn't ID 1.
        return static::find(1) ?? static::first();
    }


    public function schoolName()
    {
        // get name based on current language
        $locale = Session::get('locale', 'en'); // Get the locale from the session, with 'en' as the default
        if ($locale == 'ar') {
            return $this->getSettings()->name;
        } elseif ($locale == 'fr') {
            return $this->getSettings()->name_fr;
        } elseif ($locale == 'en') {
            return $this->getSettings()->name_en;
        } elseif ($locale == 'z'){
            return $this->getSettings()->name_z;
        } else {
            return $this->getSettings()->name_en;
        } 
    }
}
