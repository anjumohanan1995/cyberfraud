<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract; // Correct namespace
use Illuminate\Auth\Authenticatable; // Correct use statement
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Jenssegers\Mongodb\Eloquent\Model as Eloquent;
use Illuminate\Database\Eloquent\SoftDeletes;

// use Spatie\Permission\Traits\HasRoles;

class User extends Eloquent implements AuthenticatableContract
{
    use HasApiTokens, HasFactory, Notifiable, Authenticatable, SoftDeletes;

    protected $connection = 'mongodb';

    protected $collection = 'users';

    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'role',
        'sign',
        'sign_name',
        'sign_designation',
        'status'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
    public function complaints()
    {
        return $this->hasMany(Complaint::class, 'assigned_to');
    }
}
