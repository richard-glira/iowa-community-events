<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1/'], function() {
    Route::post('/create', 'api\EventsController@create');

    Route::post('/event/sign-up', 'api\EventAttendeesController@eventSignUp');

    Route::get('/event/{eventId}', 'api\EventsController@getEventById');

    Route::get('/events', 'api\EventsController@get');
    
    Route::get('/user/{id}', 'api\UsersController@fetchUserInfo');
});

