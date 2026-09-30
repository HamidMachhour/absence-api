<?php

/**
 * Created by Reliese Model.
 */

namespace App\Models;

use DateTime;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class Address
 *
 * @property string|null $id
 * @property string|null $strAddress
 * @property string|null $city
 * @property string|null $zipCode
 * @property string|null $country
 * @property string|null $location
 * @property string|null $phones
 * @property string|null $email
 * @property string|null $userId
 * @property DateTime $created_at
 * @property DateTime $updated_at
 *
 * @package App\Models
 */
class Address extends AbsenceModel
{
    protected $table = 'Address';
    

    protected $fillable = [
        'strAddress',
        'city',
        'zipCode',
        'country',
        'location',
        'phones',
        'email',
        'userId'
    ];
    public function toString()
    {
        $str = $this->strAddress . ', ' . $this->zipCode . ', ' . $this->city . ', '
            . $this->country . ', ' . $this->phones;
        return $str;
    }
    public function scopeFilter($query, array $filters)
    {
        if ($filters['id'] ?? false) {
            $query->where('id', request('id'));
        }

        if ($filters['userId'] ?? false) {
            $query->where('userId', request('userId'));
        }

        if ($filters['phone'] ?? false) {
            $query->where('phones', 'like', '%' . request('phone') . '%');
        }

        if ($filters['search'] ?? false) {
            $query->where('strAddress', 'like', '%' . request('search') . '%')
                ->orWhere('city', 'like', '%' . request('search') . '%')
                ->orWhere('zipCode', 'like', '%' . request('search') . '%')
                ->orWhere('country', 'like', '%' . request('search') . '%')
                ->orWhere('phones', 'like', '%' . request('search') . '%')
                ->orWhere('email', 'like', '%' . request('search') . '%');
        }
    }
}
