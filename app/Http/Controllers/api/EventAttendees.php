<?php
/**
 * Created by PhpStorm.
 * User: richard.garcialira
 * Date: 6/23/2019
 * Time: 5:00 PM
 */

namespace App\Http\Controllers\api;

use App\Events;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
}