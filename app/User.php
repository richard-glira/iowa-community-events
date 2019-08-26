<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {

    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable
     * 
     * @var Array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes that should be guarded
     * 
     * @var Array
     */
    protected $guarded = [];


    /**
     * The name of the table for our model.
     * 
     * @var String
     */
    protected $table = 'users';

    public function events() {
        return $this->hasOne('App\Events', 'user_id', 'id');
    }
}
