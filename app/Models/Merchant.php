<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;


class Merchant extends Eloquent
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'merchants';
    protected $fillable = [
        'merchant',
        'status',
    ];
}

