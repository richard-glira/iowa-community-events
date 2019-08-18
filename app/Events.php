<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Events extends Model {

    protected $table = 'community_events';

    protected $guarded = [];

    public function organizer() {
        return $this->hasOne('App\User', 'id', 'user_id');
    }

    public function attendees() {
        return $this->hasMany('App\EventAttendees', 'event_id', 'id');
    }
}
