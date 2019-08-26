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

    // /**
    //  * Create a new item endpoint.
    //  *
    //  * @param Request $request
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function create(Request $request) {
    //     $requestingUser = $this->findRequestingUserByAPITokens($request);
    //     $status = 400;
    //     $item = array();
    //     $errors = array();
    //     $created = false;

    //     $valid = true;
    //     $creatingSet = $request->filled('creating');
    //     $creating = $request->input('creating');
    //     if ($creatingSet && $creating === 'existing') {
    //         // validate ITEM for app or project
    //         $applications = array();
    //         $projects = array();

    //         try {
    //             $this->validate($request, [
    //                 'state' => 'required|bail',
    //             ]);
    //         } catch (\Exception $ex) {
    //             $errors[] = 'The state of an existing application or project is required';
    //             $valid = false;
    //         }

    //         if ($valid) {
    //             $productionOrDev = $request->input('state');
    //             if ($valid && $productionOrDev === 'production') {
    //                 // requires app
    //                 try {
    //                     $this->validate($request, [
    //                         'applications' => 'required|array',
    //                     ]);
    //                 } catch (\Exception $ex) {
    //                     $errors[] = 'Application IDs required';
    //                     $valid = false;
    //                 }

    //                 $project = null;
    //                 $applications = Application::whereIn('id', $request->input('applications'))->with([
    //                     'programmers'
    //                 ])->get();
    //             } elseif ($valid && $productionOrDev === 'project') {
    //                 // requires project
    //                 try {
    //                     $this->validate($request, [
    //                         'projects' => 'required|array',
    //                     ]);
    //                 } catch (\Exception $ex) {
    //                     $errors[] = 'Project IDs required';
    //                     $valid = false;
    //                 }

    //                 $application = null;
    //                 $projects = Project::whereIn('id', $request->input('projects'))->with([
    //                     'programmers'
    //                 ])->get();
    //             }

    //             if ($valid) {
    //                 try {
    //                     $this->validate($request, [
    //                         'type' => 'required|integer',
    //                         'title' => 'required|max:75',
    //                         'description' => 'required|max:10000',
    //                         'priority' => 'required|max:255',
    //                     ]);
    //                 } catch (\Exception $ex) {
    //                     $errors[] = $ex->getMessage();
    //                     $errors[] = 'A valid Type, Title, Description and Priority are required.';
    //                     $valid = false;
    //                 }

    //                 if ($valid) {
    //                     $typeId = $request->input('type');
    //                     $title = $request->input('title');
    //                     $description = $request->input('description');
    //                     $priority = $request->input('priority');
    //                     $dueDate = $request->filled('due_date') ?  $request->input('due_date') : null;
    //                     $stepsToReproduce = $request->filled('steps_to_reproduce') ? $request->input('steps_to_reproduce') : null;
    //                     $source = $request->filled('source') ? $request->input('source') : null;
    //                     $assignProgrammers = $request->filled('programmers') ? $request->input('programmers') : null;
    //                     $notifiees = $request->input('notifiees');
    //                     $tags = $request->input('tags');
    //                     $browser = $request->input('browser');
    //                     $browserVersion= $request->input('browser_version');

    //                     $validType = ItemType::find($typeId);
    //                     $validTypeId = isset($validType) ? $validType->id : null;

    //                     if (isset($validTypeId)) {
    //                         $item = new Item([
    //                             'type_id' => $validTypeId,
    //                             'title' => $title,
    //                             'description' => $this->escapeHTMLInput($description),
    //                             'priority' => strtolower($priority),
    //                             'due_date' => isset($dueDate) ? new Carbon($dueDate) : null,
    //                             'steps_to_reproduce' => isset($stepsToReproduce) ? $this->escapeHTMLInput($stepsToReproduce) : null,
    //                             'status' => Status::Todo,
    //                             'source' => $source,
    //                             'project_id' => isset($project) ? $project->id : null,
    //                             'application_id' => isset($application) ? $application->id : null,
    //                             'department' => $requestingUser->department,
    //                             'created_by' => $requestingUser->id,
    //                             'updated_by' => $requestingUser->id,
    //                             'browser' => $browser,
    //                             'browser_version' => $browserVersion,
    //                         ]);

    //                         if ($item->save()) {
    //                             $status = 202;
    //                             $created = true;
    //                             $itemNeedsToBeSaved = false;
    //                             $savedApps = array();
    //                             $programmers = collect(array());

    //                             foreach ($applications as $application) {
    //                                 $savedApps[] = \App\ItemProjectOrSystem::create([
    //                                     'item_id' => $item->id,
    //                                     'application_id' => $application->id,
    //                                     'application_or_project' => \App\Application::class
    //                                 ]);

    //                                 if ($application->programmers->count() > 0) {
    //                                     $programmers = $programmers->merge($application->programmers);
    //                                 }
    //                             }

    //                             $savedProjects = array();
    //                             foreach ($projects as $project) {
    //                                 $savedProjects[] = \App\ItemProjectOrSystem::create([
    //                                     'item_id' => $item->id,
    //                                     'project_id' => $project->id,
    //                                     'application_or_project' => \App\Project::class
    //                                 ]);

    //                                 if ($project->programmers->count() > 0) {
    //                                     $programmers = $programmers->merge($project->programmers);
    //                                 }
    //                             }

    //                             if (isset($assignProgrammers) && is_array($assignProgrammers)) {
    //                                 $assignProgrammers = collect($assignProgrammers)->map(function($programmerId) {
    //                                     return !is_int($programmerId) ? intval($programmerId) : $programmerId;
    //                                 });

    //                                 $extraProgrammers = User::whereIn('id', $assignProgrammers)->get();
    //                                 if (isset($extraProgrammers) && $extraProgrammers->isNotEmpty()) {
    //                                     $programmers = $programmers->merge($extraProgrammers);
    //                                 }
    //                             }

    //                             if ($programmers->isNotEmpty()) {
    //                                 foreach ($programmers as $assignProgrammer) {
    //                                     try {
    //                                         $item->addProgrammer($assignProgrammer);
    //                                         event(new ItemUpdated($item, 'assignment', $requestingUser, $assignProgrammer, [
    //                                             'as' => 'programmer',
    //                                         ]));
    //                                     } catch (\Exception $ex) {
    //                                         $errors[] = $ex->getMessage();
    //                                     }
    //                                 }
    //                             }

    //                             $notifyUsers = isset($notifiees) ? User::whereIn('id', $notifiees)->get() : [];
    //                             foreach ($notifyUsers as $notifiee) {
    //                                 $item->addNotifiee($notifiee);
    //                             }

    //                             $tags = isset($tags) ? Tag::whereIn('id', $tags)->get() : [];
    //                             foreach ($tags as $tag) {
    //                                 $item->addTag($tag);
    //                             }

    //                             if ($request->filled('broadcast_alert') && $request->input('broadcast_alert') == true
    //                                     && $request->filled('alert_content') && trim($request->input('alert_content')) !== '') {
    //                                 // create alert
    //                                 try {
    //                                     $alert = Alert::create([
    //                                         'type' => 'warning',
    //                                         'content' => $request->input('alert_content'),
    //                                         'is_active' => true,
    //                                         'is_dismissible' => false,
    //                                         'created_by' => $requestingUser->id,
    //                                         'updated_by' => $requestingUser->id,
    //                                         'display_to' => 'everyone',
    //                                     ]);

    //                                     $item->alert_id = $alert->id;
    //                                     $itemNeedsToBeSaved = true;
    //                                 } catch (\Exception $ex) {
    //                                     $errors[]['exception'] = $this->exceptionAPIError($ex);
    //                                 }
    //                             }

    //                             if ($itemNeedsToBeSaved) {
    //                                 $item->save();
    //                             }

    //                             $item->load([
    //                                 'createdBy',
    //                                 'manager',
    //                                 'programmers',
    //                                 'testers',
    //                                 'notifiees',
    //                                 'tags',
    //                                 'applications',
    //                                 'projects',
    //                                 'attachments',
    //                                 'activities',
    //                                 'approvals',
    //                                 'links',
    //                             ]);

    //                             // item created event
    //                             event(new ItemCreated($item, $requestingUser, []));
    //                         } else {
    //                             $errors[] = 'Failed to save item!';
    //                         }
    //                     }
    //                 }
    //             }
    //         }
    //     } elseif ($creatingSet && $creating === 'new-project') {
    //         // pass request on to the Projects API Controller...
    //         $projectsController = new ProjectsController();
    //         return $projectsController->create($request);
    //     } else {
    //         // handle error
    //         $errors[] = $creatingSet ? 'Invalid value for parameter "creating"' : 'Parameter "creating" not provided';
    //     }

    //     return $this->apiResponse([
    //         'created' => $created,
    //         'item' => is_array($item) ? $item : $item->toArray(),
    //         'errors' => $errors
    //     ], $status, $this->generateStatusMessage($status));
    // }

    // /**
    //  * Get a single Item endpoint.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function get(Request $request, $itemId) {
    //     $item = Item::withTrashed()->with([
    //         'createdBy',
    //         'updatedBy',
    //         'projects',
    //         'applications',
    //         'type',
    //         'tags',
    //         'programmers',
    //         'testers',
    //         'notifiees',
    //         'attachments',
    //         'activities',
    //         'activities.user',
    //         'activities.createdBy',
    //     ])->find($itemId);

    //     foreach ($item->attachments as $index => $attachment) {
    //         if (file_exists(storage_path() . '/app/public/user-uploads/' . substr($item->attachments[$index]->url, 22)) === false) {
    //             unset($item->attachments[$index]);
    //         }
    //     }

    //     $item->attachments = array_values($item->attachments->toArray());

    //     return $this->apiResponse([
    //         'item' => $item->toArray()
    //     ], (isset($item) ? 200 : 404));
    // }

    // /**
    //  * Get all items, up to the provided limit if one is provided
    //  *
    //  * @param int|null [$limit]
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getAll($limit = null) {
    //     if (!isset($limit)) {
    //         $items = Item::orderBy('created_at', 'asc')->get();
    //     } else {
    //         $items = Item::orderBy('created_at', 'asc')->take($limit)->get();
    //     }

    //     return $this->apiResponse([
    //         'status' => 200,
    //         'message' => 'OK',
    //         'items' => $items->toArray()
    //     ]);
    // }

    // /**
    //  * Get all items with the provided type, and the provided limit if one is provided.
    //  *
    //  * @param $type
    //  * @param int|null [$limit]
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getAllByType($type, $limit = null) {
    //     $type = strtolower($type);
    //     switch ($type) {
    //         case 'bugs':
    //             $type = 'bug';
    //             break;
    //         case 'requests':
    //             $type = 'request';
    //             break;
    //         case 'changes':
    //             $type = 'change';
    //             break;
    //         case 'incidents':
    //             $type = 'incident-report';
    //             break;
    //         case 'incident reports':
    //             $type = 'incident-report';
    //             break;
    //     }

    //     if (isset($limit)) {
    //         $items = Item::where('type', $type)->orderBy('created_at', 'asc')->take($limit)->get();
    //     } else {
    //         $items = Item::where('type', $type)->orderBy('created_at', 'asc')->get();
    //     }

    //     return $this->apiResponse([
    //         'status' => 200,
    //         'message' => 'OK',
    //         'items' => $items->toArray()
    //     ]);
    // }

    // /**
    //  * Gets all items with the provided type and status, and the provided limit if one is provided.
    //  *
    //  * @param $type
    //  * @param $status
    //  * @param int|null [$limit]
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getAllByTypeWithStatus($type, $status, $limit = null) {
    //     $type = strtolower($type);
    //     switch ($type) {
    //         case 'bugs':
    //             $type = 'bug';
    //             break;
    //         case 'requests':
    //             $type = 'request';
    //             break;
    //         case 'changes':
    //             $type = 'change';
    //             break;
    //         case 'incidents':
    //             $type = 'incident-report';
    //             break;
    //         case 'incident reports':
    //             $type = 'incident-report';
    //             break;
    //     }

    //     if (isset($limit)) {
    //         $items = Item::where(['type' => $type, 'status' => $status])->orderBy('created_at', 'asc')->take($limit)->get();
    //     } else {
    //         $items = Item::where(['type' => $type, 'status' => $status])->orderBy('created_at', 'asc')->get();
    //     }

    //     return $this->apiResponse([
    //         'status' => 200,
    //         'message' => 'OK',
    //         'items' => $items->toArray()
    //     ]);
    // }

    // /**
    //  * Gets all item types from the database and returns them.
    //  *
    //  * @param Request $request
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function getTypes(Request $request) {
    //     $types = ItemType::all();

    //     return $this->apiResponse([
    //         'types' => $types->toArray()
    //     ], 200);
    // }

    // /**
    //  * Update an item with the provided data.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function update(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $item = Item::find($itemId);
    //     $errors = array();
    //     $status = 400;
    //     $updated = false;
    //     $updatedAttributes = array();
    //     $activities = array();
    //     $blacklistAttributes = [
    //         'id',
    //         'title',
    //         'status',
    //         'created_by',
    //         'alert_id',
    //         'deleted_at',
    //     ];

    //     if ($item) {
    //         $toUpdate = $request->keys();
    //         $itemAttributes = $item->getAttributes();
    //         foreach ($toUpdate as $key) {
    //             if (array_key_exists($key, $itemAttributes) && !in_array($key, $blacklistAttributes)) {
    //                 $errorHandled = false;
    //                 $valid = true;
    //                 $value = $request->input($key);
    //                 if ($key == 'type_id' && $user->can('change-item-type')) {
    //                     $validType = ItemType::find($value);
    //                     $valid = isset($validType);
    //                 } elseif ($key == 'type_id' && !$user->can('change-item-type')) {
    //                     $valid = false;
    //                     $errors[] = "You do not have the proper permission to update '$key'";
    //                     $errorHandled = true;
    //                 }

    //                 if ($valid) {
    //                     // update
    //                     $updatedAttributes[$key] = $item->getAttribute($key);
    //                     if (strpos($key, 'date') !== false) {
    //                         $value = new Carbon($value);
    //                     }

    //                     $item->setAttribute($key, $value);
    //                     $updated = true;
    //                 } else {
    //                     if (!$errorHandled) {
    //                         $errors[] = "'$value' is not a valid value for '$key''";
    //                     }
    //                 }
    //             } else if (in_array($key, $blacklistAttributes)) {
    //                 $errors[] = "'$key' does not allow updates.";
    //             } else {
    //                 $errors[] = "Invalid attribute couldn't be updated: $key";
    //             }
    //         }

    //         if ($updated) {
    //             $item->updated_by = $user->id;
    //             if ($item->save()) {
    //                 $status = 200;
    //                 foreach ($updatedAttributes as $updatedAttribute => $oldValue) {
    //                     $newValue = $item->getAttribute($updatedAttribute);
    //                     // get related values instead of the fk's
    //                     if ($updatedAttribute == 'type_id') {
    //                         $updatedAttribute = 'item type';
    //                         if (!$validType) {
    //                             ItemType::find($oldValue);
    //                         }

    //                         $oldType = ItemType::find($oldValue);
    //                         $newValue = $validType->name;
    //                         $oldValue = isset($oldType) ? $oldType->name : $oldValue;
    //                     }

    //                     $activity = new Activity([
    //                         'item_id' => $item->id,
    //                         'action' => str_replace('_', ' ', $updatedAttribute),
    //                         'activity_type' => Activity::ITEM_EDIT,
    //                         'display_on_item' => true,
    //                         'content' => '<span class="old-value">' . $oldValue . '</span><i class="far fa-arrow-right"></i><span class="new-value">' . $newValue . '</span>',
    //                         'user_id' => $user->id,
    //                         'created_by' => $user->id,
    //                         'updated_by' => $user->id
    //                     ]);

    //                     $activity->save() ? $activity->load([
    //                         'createdBy'
    //                     ]) && $activities[] = $activity->toArray() : $errors[] = "Failed to save activity for '$updatedAttribute'";
    //                 }

    //                 $item->load([
    //                     'createdBy',
    //                     'updatedBy',
    //                     'type',
    //                     'activities',
    //                     'activities.createdBy',
    //                     'activities.user',
    //                     'attachments',
    //                     'programmers',
    //                     'testers',
    //                     'tags',
    //                     'applications.notifiees',
    //                     'projects.notifiees',
    //                     'links',
    //                     'approvals',
    //                     'approvals.createdBy',
    //                     'notifiees',
    //                 ]);
    //             }
    //         }
    //     }

    //     return $this->apiResponse([
    //         'errors' => $errors,
    //         'item' => $item->toArray(),
    //         'activities' => $activities
    //     ], $status);
    // }

    // /**
    //  * Update a single item's placement or an entire list of items' placement when $itemId is null.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function updatePlacement(Request $request, $itemId = null) {
    //     $requestingUser = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     if (isset($itemId)) {
    //         $item = Item::find($itemId);
    //         $item->placement = $request->input('placement');
    //         if (!$item->save()) {
    //             $errors[] = 'Failed to update item ' . $itemId . "'s placement index.";
    //         }

    //         return $this->apiResponse([
    //             'item' => isset($item) ? $item->toArray() : $item,
    //             'errors' => $errors
    //         ], (empty($errors) ? 202 : 400));
    //     }

    //     // loop through all items provided and update their index
    //     $rawItems = collect($request->input('items'));
    //     $items = Item::whereIn('id', $rawItems->map(function($value, $key) {
    //         return $value['id'];
    //     }))->with([
    //         'createdBy',
    //         'updatedBy',
    //         'type',
    //         'placements',
    //         'activities',
    //         'activities.createdBy',
    //         'activities.user',
    //         'attachments',
    //         'programmers',
    //         'testers',
    //         'tags',
    //         'applications',
    //         'projects',
    //         'links',
    //         'approvals',
    //         'approvals.createdBy',
    //         'notifiees',
    //     ])->get();

    //     foreach ($items as $item) {
    //         $toUpdateItemWith = $rawItems->where('id', $item->id)->first();
    //         $placement = ItemPlacement::updateOrCreate(
    //             ['item_id' => $item->id, 'status' => $toUpdateItemWith['status']],
    //             ['at' => $toUpdateItemWith['index'], 'updated_by' => $requestingUser->id]
    //         );

    //         if ($placement) {
    //             $item->updated_by = $requestingUser->id;
    //             $item->save();
    //         }
    //     }

    //     $items->load([
    //         'placements',
    //         'updatedBy',
    //     ]);

    //     return $this->apiResponse([
    //         'items' => $items->toArray(),
    //         'errors' => $errors
    //     ], (!empty($items) ? 202 : 400));
    // }

    // /**
    //  * Assigns a user as the requested assign as to the provided item.
    //  *
    //  * @param Request $request
    //  * @param null $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function addAssignee(Request $request, $itemId = null) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $triggerEvent = false;
    //     $activity = array();
    //     $assignTo = array();
    //     $assignAs = '';
    //     $errors = array();
    //     $item = array();

    //     if (isset($itemId)) {
    //         $item = Item::find($itemId);
    //         if ($request->filled('user_id') && $request->filled('as') && !is_array($request->input('user_id'))) {
    //             $assignTo = User::find($request->input('user_id'));
    //             $assignAs = $request->input('as');

    //             if ($assignAs == 'programmer') {
    //                 $triggerEvent = $item->addProgrammer($assignTo);
    //             } elseif ($assignAs == 'tester') {
    //                 $triggerEvent = $item->addTester($assignTo);
    //             } elseif ($assignAs == 'notifiee') {
    //                 $triggerEvent = $item->addNotifiee($assignTo);
    //             } elseif ($assignAs == 'manager') {
    //                 $triggerEvent = $item->changeManager($assignTo);
    //             } else {
    //                 $errors[] = 'A valid assign as is required.';
    //             }

    //             if ($triggerEvent) {
    //                 $activity = new Activity();
    //                 $activity->item_id = $item->id;
    //                 $activity->user_id = $assignTo->id;
    //                 $activity->created_by = $user->id;
    //                 $activity->updated_by = $user->id;
    //                 $activity->activity_type = 'assignment';
    //                 $activity->display_on_item = true;
    //                 $activity->action = $assignAs;
    //                 $activity->save();

    //                 event(new ItemUpdated($item, 'assignment', $user, $assignTo, [
    //                     'as' => $assignAs,
    //                 ]));

    //                 try {
    //                     $item->updated_at = Carbon::now();
    //                     $item->save();
    //                 } catch (\Exception $ex) {
    //                     // uh oh
    //                 }

    //                 $activity = $activity->load(['user', 'createdBy']);
    //                 $activity = $activity->toArray();
    //             }

    //             // get ready for response
    //             $assignTo = $assignTo->toArray();
    //             $item->load('programmers', 'testers', 'notifiees', 'manager');
    //             $item = $item->toArray();
    //         } else {
    //             $errors[] = 'A user to assign to and the type of assignment is required.';
    //         }
    //     } else {
    //         $errors[] = 'A valid item must be provided.';
    //     }

    //     return $this->apiResponse([
    //         'item' => $item,
    //         'assignee' => $assignTo,
    //         'assign_as' => $assignAs,
    //         'activity' => $triggerEvent ? $activity : [],
    //         'errors' => $errors,
    //     ], ($triggerEvent ? 202 : 400));
    // }

    // /**
    //  * Removes a user from the requested assignee type on the provided item.
    //  *
    //  * @param Request $request
    //  * @param null $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function removeAssignee(Request $request, $itemId = null) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $triggerEvent = false;
    //     $theUser = array();
    //     $removeFrom = '';
    //     $errors = array();
    //     $item = array();
    //     $activity = array();

    //     if (isset($itemId)) {
    //         $item = Item::find($itemId);
    //         if ($request->filled('user_id') && $request->filled('as') && !is_array($request->input('user_id'))) {
    //             $theUser = User::find($request->input('user_id'));
    //             $removeFrom = $request->input('as');

    //             if ($removeFrom == 'programmer') {
    //                 $triggerEvent = $item->removeProgrammer($theUser);
    //             } elseif ($removeFrom == 'tester') {
    //                 $triggerEvent = $item->removeTester($theUser);
    //             } elseif ($removeFrom == 'notifiee') {
    //                 $triggerEvent = $item->addNotifiee($theUser);
    //             } elseif ($removeFrom == 'manager') {
    //                 $triggerEvent = $item->changeManager($theUser);
    //             } else {
    //                 $errors[] = 'A valid assign as is required.';
    //             }

    //             if ($triggerEvent) {
    //                 $activity = new Activity();
    //                 $activity->item_id = $item->id;
    //                 $activity->user_id = $theUser->id;
    //                 $activity->created_by = $user->id;
    //                 $activity->updated_by = $user->id;
    //                 $activity->activity_type = 'un-assignment';
    //                 $activity->display_on_item = true;
    //                 $activity->action = $removeFrom;
    //                 if ($activity->save()) {
    //                     $activity->load([
    //                         'createdBy',
    //                         'user',
    //                     ]);
    //                     $activity = $activity->toArray();
    //                 }

    //                 try {
    //                     $item->updated_at = Carbon::now();
    //                     $item->save();
    //                 } catch (\Exception $ex) {
    //                     // uh oh
    //                 }
    //             }

    //             // get ready for response
    //             $theUser = $theUser->toArray();
    //             $item->load('programmers', 'testers', 'notifiees', 'manager');
    //             $item = $item->toArray();
    //         } else {
    //             $errors[] = 'A user to remove the assignment from and the type of assignment are required.';
    //         }
    //     } else {
    //         $errors[] = 'A valid item must be provided.';
    //     }

    //     return $this->apiResponse([
    //         'item' => $item,
    //         'assignee' => $theUser,
    //         'remove_from' => $removeFrom,
    //         'activity' => $activity,
    //         'errors' => $errors,
    //     ], ($triggerEvent ? 202 : 400));
    // }

    // /**
    //  * Add or Remove the provided tag on the provided item.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function updateTag(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     $activity = array();
    //     $item = Item::find($itemId);
    //     $tagId = $request->filled('tag_id') ? $request->input('tag_id') : null;
    //     $action = $request->filled('action') ? $request->input('action') : 'add';
    //     $tag = isset($tagId) ? Tag::find($tagId) : null;
    //     $saved = false;

    //     if ($item->status != Status::Done) {
    //         if (isset($item) && isset($tag)) {
    //             if ($action == 'add') {
    //                 $saved = $item->addTag($tag);
    //             } elseif ($action == 'remove') {
    //                 $saved = $item->removeTag($tag);
    //             } else {
    //                 $errors[] = 'The requested tag action is invalid.';
    //             }
    //         } else {
    //             $errors[] = 'A valid Item ID and Tag ID are required.';
    //         }
    //     } else {
    //         $errors[] = 'Tags can\'t be modified once an item is closed.';
    //     }

    //     if ($saved) {
    //         $activity = new Activity();
    //         $activity->item_id = $item->id;
    //         $activity->user_id = $user->id;
    //         $activity->created_by = $user->id;
    //         $activity->updated_by = $user->id;
    //         $activity->activity_type = $action == 'add' ? Activity::TAGGED : Activity::UNTAGGED;
    //         $activity->display_on_item = true;
    //         $activity->action = $tag->name;
    //         $activity->save();

    //         try {
    //             $item->updated_at = Carbon::now();
    //             $item->updated_by = $user->id;
    //             $item->save();
    //         } catch (\Exception $ex) {
    //             // uh oh
    //         }

    //         $item->load([
    //             'activities',
    //             'updatedBy',
    //             'tags',
    //         ]);

    //         $item = $item->toArray();
    //         $tag = $tag->toArray();
    //         $activity->load([
    //             'createdBy',
    //             'user',
    //             'item',
    //         ]);

    //         $activity = $activity->toArray();
    //     }

    //     return $this->apiResponse([
    //         'tag' => $tag,
    //         'item' => $item,
    //         'activity' => $activity,
    //         'errors' => $errors,
    //     ], ($saved ? 202 : 400));
    // }

    // /**
    //  * Add or Remove the provided project or system to the provided item.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function modifyProjectOrSystem(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     $data = array();
    //     $item = Item::find($itemId);
    //     $appId = $request->filled('application_id') ? $request->input('application_id') : null;
    //     $projectId = $request->filled('project_id') ? $request->input('project_id') : null;
    //     $action = $request->filled('action') ? $request->input('action') : 'add';
    //     $saved = false;

    //     if (isset($item) && isset($appId)) {
    //         $app = Application::find($appId);
    //         try {
    //             if ($action == 'add') {
    //                 $saved = $item->addApplication($app);
    //                 if ($saved !== true && gettype($saved) == 'string') {
    //                     $errors[] = $saved;
    //                 }
    //             } elseif ($action == 'remove' && IMTUser::checkIfUserHasManagerPermission($user)) {
    //                 $saved = $item->removeApplication($app);
    //             } elseif ($action == 'remove' && !IMTUser::checkIfUserHasManagerPermission($user)) {
    //                 $errors[] = 'You do not have the proper permission to perform this action.';
    //             } else {
    //                 $errors[] = 'The requested action is invalid.';
    //             }
    //         } catch (\Exception $ex) {
    //             $errors[] = $ex->getMessage();
    //         }
    //     } elseif (isset($item) && isset($projectId)) {
    //         $project = Project::find($projectId);
    //         try {
    //             if ($action == 'add') {
    //                 $saved = $item->addProject($project);
    //                 if ($saved !== true && gettype($saved) == 'string') {
    //                     $errors[] = $saved;
    //                 }
    //             } elseif ($action == 'remove' && IMTUser::checkIfUserHasManagerPermission($user)) {
    //                 $saved = $item->removeProject($project);
    //             } elseif ($action == 'remove' && !IMTUser::checkIfUserHasManagerPermission($user)) {
    //                 $errors[] = 'You do not have the proper permission to perform this action.';
    //             } else {
    //                 $errors[] = 'The requested action is invalid.';
    //             }
    //         } catch (\Exception $ex) {
    //             $errors[] = $ex->getMessage();
    //         }
    //     } else {
    //         $errors[] = 'A valid Item ID and Project ID or System ID are required.';
    //     }

    //     if ($saved === true) {
    //         try {
    //             $item->updated_by = $user->id;
    //             $item->updated_at = Carbon::now();
    //             $item->save();

    //             $activity = new Activity();
    //             $activity->item_id = $item->id;
    //             $activity->user_id = $user->id;
    //             $activity->created_by = $user->id;
    //             $activity->updated_by = $user->id;
    //             $activity->activity_type = Activity::ADD_OR_REMOVE_PROJECT_OR_SYSTEM;
    //             $activity->display_on_item = true;
    //             $activity->action = $action . ($action == 'add' ? 'ed' : 'd');
    //             $activity->content = isset($app) ? $app->title : $project->title;
    //             $activity->save();
    //             event(new ItemCreated($item, $user, [
    //                     'createdChanged' => 'changed',
    //                 ]));

    //         } catch (\Exception $ex) {
    //             // uh oh
    //         }

    //         $item->load([
    //             'applications',
    //             'projects',
    //             'tags',
    //             'activities',
    //             'activities.createdBy',
    //             'activities.user',
    //         ]);

    //         $item = $item->toArray();
    //         $data = [
    //             'item' => $item,
    //             'errors' => $errors,
    //         ];

    //         if (isset($appId)) {
    //             $app = $app->toArray();
    //             $data['application'] = $app;
    //         } elseif (isset($projectId)) {
    //             $project = $project->toArray();
    //             $data['project'] = $project;
    //         }
    //     } else {
    //         $data['errors'] = $errors;
    //     }

    //     return $this->apiResponse($data, ($saved === true ? 202 : 400));
    // }

    // /**
    //  * Subscribe (or un-subscribe) the requesting user to the provided item.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function subscribe(Request $request, $itemId) {
    //     $requestingUser = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     $item = Item::find($itemId);
    //     $action = $request->filled('action') ? $request->input('action') : 'subscribe';
    //     $specificUserId = $request->filled('user_id') ? $request->input('user_id') : null;
    //     $saved = false;
    //     $status = 400;

    //     if (isset($item)) {
    //         if (isset($specificUserId)) {
    //             $user = User::find($specificUserId);
    //         } else {
    //             $user = $requestingUser;
    //         }

    //         if (isset($user)) {
    //             if ($action == 'subscribe') {
    //                 $saved = $item->addNotifiee($user);
    //                 event(new ItemUpdated($item, 'subscribe', $requestingUser, $user, [
    //                     'as' => 'subscriber',
    //                 ]));
    //             } elseif ($action == 'unsubscribe') {
    //                 $saved = $item->removeNotifiee($user);
    //             } else {
    //                 $errors[] = 'The requested subscription action is invalid.';
    //             }
    //         } else {
    //             $errors[] = 'A valid User ID is required.';
    //         }
    //     } else {
    //         $errors[] = 'A valid Item ID is required.';
    //     }

    //     if ($saved) {
    //         $item->load([
    //             'notifiees',
    //         ]);

    //         $status = 202;
    //     } else if ($saved === null) {
    //         $status = 304;
    //     }

    //     $item = $item->toArray();
    //     $user = $user->toArray();
    //     return $this->apiResponse([
    //         'action' => $action . 'd',
    //         'subscriber' => $user,
    //         'item' => $item,
    //         'errors' => $errors,
    //     ], $status);
    // }

    // /**
    //  * Add the provided link to the provided item.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function link(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     $activity = array();
    //     $itemLink = array();
    //     $item = Item::find($itemId);
    //     $link = $request->filled('link') ? $request->input('link') : null;
    //     $type = $request->filled('type') ? $request->input('type') : null;
    //     $action = $request->filled('action') ? $request->input('action') : 'add';
    //     $saved = false;

    //     if (isset($item) && isset($type) && (isset($link) || $action == 'remove')) {
    //         $valid = true;
    //         if ($action != 'remove') {
    //             // validate link
    //             try {
    //                 $this->validate($request, [
    //                     'link' => 'required|url',
    //                 ]);
    //             } catch (\Exception $ex) {
    //                 $errors[] = 'A valid link URL is required';
    //                 $valid = false;
    //             }
    //         }

    //         if ($valid && ($type == 'gitlab_merge_url' || $type == 'test_environment')) {
    //             if ($action == 'add') {
    //                 $saved = $item->addLink($type, $link, $user);
    //             } elseif ($action == 'remove') {
    //                 $itemLink = ItemLink::find($link);
    //                 if (isset($itemLink)) {
    //                     $saved = $item->removeLink($itemLink);
    //                 } else {
    //                     $errors[] = 'A valid link ID must be provided to remove the link.';
    //                 }
    //             } else {
    //                 $errors[] = 'A valid action must be provided.';
    //             }
    //         } else {
    //             if ($valid) {
    //                 $errors[] = 'The requested link type is invalid.';
    //             }
    //         }
    //     } else {
    //         $errors[] = 'A valid Item ID, link type, and link are required.';
    //     }

    //     if ($saved) {
    //         switch ($type) {
    //             case 'test_environment':
    //                 $linkNameForHumans = 'Test Environment';
    //                 break;
    //             case 'gitlab_merge_url':
    //             default:
    //                 $linkNameForHumans = 'GitLab Merge';
    //                 break;
    //         }

    //         $activity = new Activity([
    //             'item_id' => $item->id,
    //             'user_id' => $user->id,
    //             'created_by' => $user->id,
    //             'updated_by' => $user->id,
    //             'activity_type' => Activity::LINKED,
    //             'content' => $linkNameForHumans,
    //             'display_on_item' => true,
    //             'action' => $action == 'add' ? $action . 'ed' : $action . 'd',
    //         ]);

    //         $savedActivity = $activity->save();
    //         try {
    //             $item->updated_at = Carbon::now();
    //             $item->save();
    //         } catch (\Exception $ex) {
    //             // uh oh
    //         }

    //         if ($savedActivity) {
    //             $activity->load([
    //                 'createdBy',
    //                 'user',
    //                 'item',
    //             ]);
    //         }

    //         $item = $item->toArray();
    //         $activity = $savedActivity ? $activity->toArray() : [];
    //         if (!is_array($itemLink)) {
    //             $itemLink = $itemLink->toArray();
    //         } else {
    //             // for when the link is added, it's under the saved variable...
    //             $itemLink = $saved->toArray();
    //         }
    //     }

    //     return $this->apiResponse([
    //         'action' => $action,
    //         'item' => $item,
    //         'link' => $itemLink,
    //         'activity' => $activity,
    //         'errors' => $errors,
    //     ], ($saved ? 202 : 400));
    // }

    // /**
    //  * Change the priority of the provided item.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function changePriority(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $errors = array();
    //     $item = Item::find($itemId);
    //     $changeTo = $request->filled('to') ? $request->input('to') : null;
    //     $saved = false;
    //     $activity = array();

    //     if (isset($item) && isset($changeTo)) {
    //         if ($changeTo == Item::PRIORITY_LOW ||
    //                 $changeTo == Item::PRIORITY_MEDIUM ||
    //                 $changeTo == Item::PRIORITY_HIGH ||
    //                 $changeTo == Item::PRIORITY_CRITICAL) {
    //             $item->priority = $changeTo;
    //             $item->updated_by = $user->id;
    //             $saved = $item->save();

    //             if ($saved) {
    //                 $activity = new Activity();
    //                 $activity->item_id = $item->id;
    //                 $activity->user_id = $user->id;
    //                 $activity->created_by = $user->id;
    //                 $activity->updated_by = $user->id;
    //                 $activity->action = 'changed the priority';
    //                 $activity->activity_type = Activity::CHANGE_PRIORITY;
    //                 $activity->display_on_item = true;
    //                 $activity->content = $changeTo;
    //                 if ($activity->save()) {
    //                     $activity->load([
    //                         'createdBy'
    //                     ]);

    //                     $activity = $activity->toArray();
    //                 }
    //             }
    //         } else {
    //             $errors[] = 'The requested priority to change to is invalid.';
    //         }
    //     } else {
    //         $errors[] = 'A valid Item ID is required.';
    //     }

    //     if ($saved) {
    //         $item->load([
    //             'createdBy',
    //             'updatedBy',
    //             'type',
    //             'activities',
    //             'activities.createdBy',
    //             'activities.user',
    //             'attachments',
    //             'programmers',
    //             'testers',
    //             'tags',
    //             'applications.notifiees',
    //             'projects.notifiees',
    //             'links',
    //             'approvals',
    //             'approvals.createdBy',
    //             'notifiees',
    //         ]);
    //     }

    //     $item = $item->toArray();
    //     return $this->apiResponse([
    //         'priority' => $changeTo,
    //         'item' => $item,
    //         'activity' => $activity,
    //         'errors' => $errors,
    //     ], ($saved ? 202 : 400));
    // }

    // /**
    //  * POST /api/v1/item/{itemId}/convert/project
    //  * Converts the provided item to a project.
    //  *
    //  * @param Request $request
    //  * @param $itemId
    //  *
    //  * @return \Illuminate\Http\JsonResponse
    //  */
    // public function convertToProject(Request $request, $itemId) {
    //     $user = $this->findRequestingUserByAPITokens($request);
    //     $saved = false;
    //     $errors = array();
    //     $project = array();

    //     if ($user->can('project-manager')) {
    //         $item = Item::with([
    //             'type',
    //             'applications',
    //         ])->find($itemId);

    //         if (isset($item)) {
    //             try {
    //                 $project = new Project([
    //                     'title' => $item->title,
    //                     'description' => $item->description . ' (Converted from ' . $item->type->name . ' #' . $item->id . ' with status of ' . ucwords($item->status) . ')',
    //                     'created_by' => $item->created_by,
    //                     'updated_by' => $user->id,
    //                     'classification' => $request->filled('classification') ? $request->input('classification') : '',
    //                     'priority' => $request->filled('priority') ? $request->input('priority')  :'unknown',
    //                     'business_case' => $request->filled('business_case') ? $request->input('business_case') : 'Not provided (converted from item)',
    //                     'alternative_options' => $request->filled('alertnative_options') ? $request->input('alertnative_options') : 'Not provided (converted from item)',
    //                     'risk' => $request->filled('risk') ? $request->input('risk') : 'Not provided (converted from item)',
    //                     'date_needed_by' => $request->filled('date_needed_by') ? new Carbon($request->input('date_needed_by')) : $item->due_date,
    //                     'estimated_time' => $request->filled('estimated_time') ? $request->input('estimated_time') : 'Not Sure',
    //                     'new_or_existing_application' => $item->applications->count() > 0 ? 'Existing System' : 'New System',
    //                     'review_stage' => 'initial',
    //                     'status_id' => ProjectStatus::where('title', 'New')->first()->id,
    //                     'converted_from_item_id' => $item->id,
    //                 ]);

    //                 $saved = $project->save();
    //                 if ($saved) {
    //                     if ($item->applications->count() > 0) {
    //                         foreach ($item->applications as $application) {
    //                             ProjectExistingApplication::create([
    //                                 'project_id' => $project->id,
    //                                 'application_id' => $application->id
    //                             ]);
    //                         }
    //                     }

    //                     $item->delete();
    //                     try {
    //                         event(new ProjectRequested($project, [
    //                             'from' => $user,
    //                             'prepend_subject' => '[CONVERTED FROM ITEM]'
    //                         ]));
    //                     } catch (\Exception $ex) {
    //                         $status = 400;
    //                         $errors[] = 'An error occurred but the project request was created.';
    //                         $errors[] = $this->exceptionAPIError($ex);
    //                     }
    //                 }

    //                 $project = $project->toArray();
    //             } catch (\Exception $ex) {
    //                 $errors[] = $ex->getMessage();
    //             }
    //         } else {
    //             $errors[] = 'A valid Item ID is required.';
    //         }
    //     } else {
    //         $errors[] = 'You don\'t have permission to do that.';
    //     }

    //     return $this->apiResponse([
    //         'project' => $project,
    //         'errors' => $errors,
    //     ], ($saved ? 202 : 400));
    // }
}
