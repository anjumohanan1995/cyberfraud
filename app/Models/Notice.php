<?php

namespace App\Models;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Eloquent
{
    use HasFactory;

    protected $connection = 'mongodb';
    protected $collection = 'notice';

    protected $fillable = [
        // 'notice_content', 'number', 'url', 'domain_name', 'domain_id', 'sub'


        'notice_type',
        'ack_number',
        'case_number',
        'url',
        'content',
        'user_id',
        'approve_id',
        'assing_by_user_id',
        'assing_to_user_id',
        'type',
        'account_no',
        'bank',
        'evidence_type',
        'ip',
        'domain',
        'source_type'



    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

        // Relationship to the user who approved the notice
        public function approvedBy()
        {
            return $this->belongsTo(User::class, 'approve_id');
        }
        public function  assingTo()
        {
            return $this->belongsTo(User::class, 'assing_to_user_id');
        }

    }

