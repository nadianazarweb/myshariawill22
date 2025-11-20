<?php

namespace App\Models;

use App\Level;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Traits\RefKey;
use App\Report;


class User extends \TCG\Voyager\Models\User
{
    use HasApiTokens, HasFactory, Notifiable,RefKey;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function role_detail(){
        return $this->belongsTo(\TCG\Voyager\Models\Role::class , 'role_id');
    }

    public function userdetail()
    {
        return $this->belongsTo(User::class , 'created_by');
    }
}
