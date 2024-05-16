<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

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

        'entry_date' => 'date',

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
        // 'entry_date',
        'current_status',
        'date_of_action',
        'action_taken_by_name',
        'action_taken_by_designation',
        'action_taken_by_mobile',
        'action_taken_by_email',
        'action_taken_by_bank',
    ];
}
