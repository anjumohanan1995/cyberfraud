<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Wallet extends Eloquent
{
    use HasFactory, SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'wallets';
    protected $fillable = [
        'wallet',
        'status',
    ];
}

