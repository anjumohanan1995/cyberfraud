<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class ComplaintOthers extends Eloquent
{

    use SoftDeletes;

    protected $connection = 'mongodb';

    protected $guarded = [];
    protected $collection = 'complaint_others';
    
    public function sourceType() {
        // Correct relationship: belongsTo, where 'source_type' in complaints references '_id' in sourcetype
        return $this->belongsTo(SourceType::class, 'source_type', '_id');
    }
}
