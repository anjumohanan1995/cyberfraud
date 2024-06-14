<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Registrar extends Eloquent
{

    use SoftDeletes;

    protected $connection = 'mongodb';
    protected $collection = 'registrar_data';

    protected $guarded = [];


}

