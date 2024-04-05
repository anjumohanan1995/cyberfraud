<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class RolePermission extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'role_permissions';

    /**
     * The attributes which are mass assigned will be used.
     *
     * It will return @var array
     */
    protected $fillable = [
        'role','user_id','permission','sub_permissions'
    ];

}