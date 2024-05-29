<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ComplaintAdditionalData extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'complaint_additional_data';

    protected $fillable = [
        'ack_no',
        'fir_doc',
        'age',
        'profession'
    ];
}
