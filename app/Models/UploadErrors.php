<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
class UploadErrors extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'uploaderrors';

    protected $fillable = [
        'user_id', 'upload_id', 'error'
    ];
}
