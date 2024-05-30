<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class ComplaintOthers extends Eloquent
{
 
    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $guarded = [];


}
