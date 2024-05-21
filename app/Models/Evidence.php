<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evidence extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'evidence';

    protected $fillable = [
        'ack_no',
        'evidence_type',
        'url',
        'domain',
        'registry_details',
        'ip',
        'registrar',
        'pdf',
        'screenshots',
        'remarks'
    ];
}
