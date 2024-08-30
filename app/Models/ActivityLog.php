<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class ActivityLog extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'activity_logs';

    protected $fillable = [
        'remover_id',
        'remover_role',
        'removed_id',
        'removed_name',
        'remover_name',

    ];
}
