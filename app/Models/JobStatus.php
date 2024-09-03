<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class JobStatus extends Eloquent
{
    use HasFactory;
    protected $connection = 'mongodb'; // Define your MongoDB connection
    protected $collection = 'job_statuses'; // Define your collection name
    protected $fillable = ['job_id', 'status','error_message'];
}
