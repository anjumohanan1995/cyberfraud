<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class SourceType extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'sourcetype';

    /**
     * The attributes which are mass assigned will be used.
     *
     * It will return @var array
     */
    protected $fillable = [
        'name',
        'status'
    ];

    public function complaints() {
        // Correct relationship: hasMany, where 'source_type' in complaints matches '_id' in sourcetype
        return $this->hasMany(ComplaintOthers::class, 'source_type', '_id');
    }

}
