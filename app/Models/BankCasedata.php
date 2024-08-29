<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class BankCasedata extends Eloquent
{
    use HasFactory , SoftDeletes ;

    protected $connection = 'mongodb';

    protected $collection = 'bank_casedata';

    protected $guarded = [];
    protected $casts = [

        'transaction_date' => 'datetime',
        // 'date_of_action' => 'datetime',

    ];

        // Add this relationship method
        public function complaint()
        {
            return $this->belongsTo(Complaint::class, 'acknowledgement_no', 'acknowledgement_no');
        }


}
