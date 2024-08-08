<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class Complaint extends Eloquent
{
    use SoftDeletes;

    protected $connection = 'mongodb';

    /**
     * The attributes which are mass assigned will be used.
     *
     * It will return @var array
     */

    protected $casts = [

        'entry_date' => 'datetime',
        'date_of_action' => 'datetime',
        'status_changed' => 'datetime',


    ];
    protected $fillable = [
        'source_type',
        'acknowledgement_no',
        'district',
        'police_station',
        'complainant_name',
        'complainant_mobile',
        'transaction_id',
        'bank_name',
        'account_id',
        'amount',
        'current_status',
        'date_of_action',
        'action_taken_by_name',
        'action_taken_by_designation',
        'action_taken_by_mobile',
        'action_taken_by_email',
        'action_taken_by_bank',
        'com_status',
        'assigned_to',
        'case_status',
        'status_changed'
    ];

        // Define the relationship with BankCasedata
        public function bankCaseData()
        {
            return $this->hasOne(BankCasedata::class, 'transaction_id_or_utr_no', 'transaction_id');
        }

        // // Define mutator for entry_date
        // public function setEntryDateAttribute($value)
        // {
        //     // Assuming entry_date is stored in a 'entry_date' field in the database
        //     $this->attributes['entry_date'] = Carbon::createFromFormat('d-m-Y, h:i A', $value)->format('Y-m-d H:i:s');
        // }

        // // Define accessor if needed
        // public function getEntryDateAttribute($value)
        // {
        //     return Carbon::parse($value)->format('d-m-Y, h:i A');
        // }
}
