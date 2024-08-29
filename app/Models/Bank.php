<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

class Bank extends Eloquent
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'banks';
    protected $fillable = [
        'bank',
        'status',
    ];
}

