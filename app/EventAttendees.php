<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EventAttendees extends Model {

    protected $table = 'event_attendees';

    protected $guarded = [];

    public function events() {
        return $this->hasMany('App\Events', 'event_id', 'id');
    }
}
