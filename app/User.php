<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Model {

    protected $table = 'users';

    protected $guarded = [];

    public function events() {
        return $this->hasOne('App\Events', 'user_id', 'id');
    }
}
