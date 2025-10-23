<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Session;

class ProposedAction extends Model
{
    protected $table = 'ProposedAction';

    protected $fillable = ['name_ar', 'name_fr', 'name_en', 'name_z'];

    public function reports(): BelongsToMany
    {
        return $this->belongsToMany(MisconductReport::class, 'ReportProposedAction');
    }


    public function actionName()
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
