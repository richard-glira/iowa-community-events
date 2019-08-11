<?php

namespace App\Http\Controllers;

use App\Events;
use App\Http\Resources\Events as EventsResources;
use Illuminate\Http\Request;
use Illuminate\Console\Scheduling\Event;

class EventsController extends Controller {
    
    /**
     * Create a new community event.
     * 
     * @param $request 
     * @return JSON
     */
    public function create($request) {
        $status = 400;
        $message = 'Bad Request';

        $eventName = $request->filled('event_name') ? $request->input('event_name') : '';
        $description = $request->filled('description') ? $request->input('description') : '';
        $category = $request->filled('category') ? $request->input('category') : '';
        $location = $request->filled('location') ? $request->input('location') : '';
        $eventTime =  $request->filled('event_time') ? $request->input('event_time') : '';
        $eventDate = $request->filled('event_date') ? $request->filled('event_date') : '';
        $email = $request->filled('email') ? $request->filled('email') : '';
        $isValid = true;

        if ($eventDate !== '' || $description !== '' || $category !== '' || $location !== '' || $eventTime !== '' || $eventDate !== '' || $email !== '') {
            $isValid = false;
        }

        if ($isValid) {
            $event = Events::create([
                'event_name' => $eventName,
                'description' => $description,
                'category' => $category,
                'location' => $location,
                'event_time' => $eventTime,
                'event_date' => $eventDate,
                'email' => $email,
            ]);

            if ($event->save()) {
                $status = 200;
                $message = 'OK';
            }
        } else {
            $status = 406;
            $message = 'Not Acceptable';
        }

        return [
            'status' => $status,
            'message' => $message,
            'data' => $request->all()
        ];
    }

    public function get() {
        $events = Events::paginate(5);

        return EventsResources::collection($events)->additional(['meta' => [
            'version' => '1.0.0',
            'API_base_url' => url('/')
        ]]);
    }
}
