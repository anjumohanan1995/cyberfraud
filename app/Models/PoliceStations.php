<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class PoliceStations extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';

    protected $collection = 'policestations';

    protected $fillable = ['name', 'district_id', 'place', 'address', 'phone'];

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id');
    }


}
