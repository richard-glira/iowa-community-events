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

/**
 * Class ItemController
 * All Item creation/modification/deletion endpoint logic.
 *
 * @package App\Http\Controllers\api
 */
class EventAttendeesController extends APIController {

    /**
     * Get all Events for user endpoint.
     *
     * @param Request $request
     * @param $id
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function get(Request $request) {
        $events = Events::paginate(5);

        return $this->apiResponse([
            'events' => $events->toArray()
        ], (isset($events) ? 200 : 404));
    }

    /**
     * Creates event attendee with the passed user parameter.
     *
     * @param Request $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function eventSignUp(Request $request) {
        $status = 400;
        $eventSignUp = array();
        $errors = array();
        $created = false;
        $valid = true;

        // TODO: Validate JWT API token 
        if ($valid) {
            $eventId = $request->has('event_id') ? $request->input('event_id') : '';
            $userId = $request->has('user_id') ? $request->input('user_id') : '';

            $user = User::find($userId);
            $event = Events::find($eventId);

            if (!is_null($user) && !is_null($event)) {
                $eventSignUp = new EventAttendees([
                    'event_id' => $eventId,
                    'user_id' => $userId
                ]);
    
                if ($eventSignUp->save()) {
                    $status = 202;
                    $created = true;
                } else {
                    $errors[] = 'Failed to sign user for event ' . $eventId . '!';
                }
            } else {
                $errors[] = 'Failed to sign user to event, Event/User does not exist: EVENT{' . $eventId . '} / USER{' . $userId . '}'; 
            }
        } else {
            $errors[] = 'Missing authentication token.';
        }

        return $this->apiResponse([
            'created' => $created,
            'event' => is_array($eventSignUp) ? $eventSignUp : $eventSignUp->toArray(),
            'errors' => $errors
        ], $status, $this->generateStatusMessage($status));
    }
}