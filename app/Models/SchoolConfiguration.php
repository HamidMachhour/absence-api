<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
}
