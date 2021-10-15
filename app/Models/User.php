<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

use Nicolaslopezj\Searchable\SearchableTrait;


use App\Models\queue;



class User extends Authenticatable
{
    use HasFactory, Notifiable,HasRoles,SearchableTrait;


    public $timestamps = false;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $dateFormat = 'd-m-Y H:i:s'; 
    

    protected $searchable = [
        'columns' => [
            'users.name'  => 5,
            'users.email'   => 5
        ]
    ];



    protected $fillable = [
        'name',
        'email',
        'password',
        'organization_id'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function queues(){

        return $this->belongsToMany(queue::class,'queue_users');
    }

}
