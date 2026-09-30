<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class MisconductAct extends Model
{
    protected $table = 'MisconductAction';

    protected $fillable = ['name_ar', 'name_fr', 'name_en', 'name_z'];

    protected static function booted()
    {

        static::deleting(function ($misconduct_action) {
            // Use a transaction to ensure data integrity
            DB::transaction(function () use ($misconduct_action) {
                $misconduct_action->reports()->detach();
            });
        });
    }
    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(MisconductReport::class, 'ReportMisconductAction');
    }

    public function actName()
    {
        $locale = Session::get('locale', 'en'); // Get the locale from the session, with 'en' as the default

        if ($locale == 'ar') {
            return $this->name_ar;
        } elseif ($locale == 'fr') {
            return $this->name_fr;
        } elseif ($locale == 'en') {
            return $this->name_en;
        }
        return $this->name_z;
    }
}
