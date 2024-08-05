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


        'sub',
        'ack_number',
        'url',
        'content',
        'user_id'
        // 'main_content',
        // 'content_1',
        // 'content_2',
        // 'url_head',
        // 'url',
        // 'domain_name',
        // 'domain_id',
        // 'details_head',
        // 'details_content',
        // 'footer_content',
        // 'user_id',
        // 'content'


    ];
    }
