<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Modus extends Eloquent
{

    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'modus';

    /**
     * The attributes which are mass assigned will be used.
     *
     * It will return @var array
     */
    protected $fillable = [
        'name',
        'status',
    ];
}
