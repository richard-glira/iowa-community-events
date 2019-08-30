<?php
/**
 * Created by PhpStorm.
 * User: richard.garcialira
 * Date: 6/23/2019
 * Time: 5:00 PM
 */

namespace App\Http\Controllers\api;

use App\User;
use App\Events;
use App\EventAttendees;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;


/**
 * Class ItemController
 * All Item creation/modification/deletion endpoint logic.
 *
 * @package App\Http\Controllers\api
 */
class EventsController extends APIController {

    /**
     * Get all Events for user endpoint.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request) {
        $status = 400;
        $event = array();
        $attendees = array();
        $errors = array();
        $created = false;
        $valid = true;
        
        if ($valid) {
            // Requires user info object to tie with event
            try {
                $this->validate($request, [
                    'organizer' => 'required|array',
                ]);
            } catch (\Exception $ex) {
                $errors[] = 'Organizer is required';
                $valid = false;
            }

            if ($valid) {
                // Validate title & description 
                try {
                    $this->validate($request, [
                        'title' => 'required|max:75',
                        'description' => 'required|max:500',
                    ]);
                } catch (\Exception $ex) {
                    $errors[] = $ex->getMessage();
                    $errors[] = 'A valid Title, and Description are required.';
                    $valid = false;
                }

                if ($valid) {
                    $eventName = $request->has('title') ? $request->input('title') : '';
                    $description = $request->has('description') ? $request->input('description') : '';
                    $category = $request->has('category') ? $request->input('category') : '';
                    $location = $request->has('location') ? $request->input('location') : '';
                    $eventTime = $request->has('time') ? $request->input('time') : '';
                    $eventDate = $request->has('date') ? $request->input('date') : '';
                    $email = $request->has('email') ? $request->input('email') : '';
                    $userId = 0;
                    $organizer = $request->has('organizer') ? $request->input('organizer') : '';
                    $userId = is_array($request->input('organizer')) && array_key_exists('id', $request->input('organizer')) ? $organizer['id'] : 1;
        
                    $event = new Events([
                        'event_name' => $eventName,
                        'description' => $description,
                        'category' => $category,
                        'location' => $location,
                        'event_time' => $eventTime,
                        'event_date' => $eventDate,
                        'email' => $email,
                        'user_id' => $userId
                    ]);
            
                    if ($event->save()) {
                        $status = 202;
                        $created = true;

                        $attendees = new EventAttendees([
                            'event_id' => $event->id,
                            'user_id' => $userId
                        ]);

                        if ($attendees->save()) {
                            $status = 202;
                            $created = true;
                        } else {
                            $errors[] = 'Failed to create attendee!';
                        }
                    } else {
                        $errors[] = 'Failed to create event!';
                    }
                }
            }
        } else {
            $errors[] = 'Missing authentication token.';
        }

        return $this->apiResponse([
            'created' => $created,
            'event' => is_array($event) ? $event : $event->toArray(),
            'attendees' => is_array($attendees) ? $attendees : $attendees->toArray(),
            'errors' => $errors
        ], $status, $this->generateStatusMessage($status));
    }

    /**
     * Delete passed event.
     * 
     * @param Request $request
     * @param $id 
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function delete(Request $request, $eventId) {
        $status = 400;

        $event = Events::find($eventId);
        $status = $event->delete() ? 200 : 404;

        return $this->apiResponse([
            'event' => $event
        ], $status, $this->generateStatusMessage($status));
    }

    /**
     * Get all Events for user endpoint.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request) {
        $limit = $request->has('limit') ? $request->input('limit') : 4;
        $page = $request->has('page') ? $request->input('page') : 1;

        $events = Events::with(['attendees'])->paginate($limit);

        return $this->apiResponse([
            'events' => $events
        ], (isset($events) ? 200 : 404));
    }

    /**
     * Get single Events endpoint
     * 
     * @param Request $request
     * @param $id
     * 
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEventById(Request $request, $eventId) {
        $event = Events::find($eventId);

        return $this->apiResponse([
            'event' => $event
        ], (isset($event) ? 200 : 404));
    }
}
