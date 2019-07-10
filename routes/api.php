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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});


 Route::get('/events', 'API\EventsController@getAll');

Route::get('/events/{event_id}', 'API\EventsController@getEvent')
        ->where(['event_id' => '[0-9]+']);
Route::post('/events/add-new', 'API\EventsController@addNew');
Route::post('/events/{event_id}/update', 'API\EventsController@update')
    ->where(['event_id' => '[0-9]+']);

Route::post('/events/{event_id}/add-tickets', 'API\EventsController@addTickets')
    ->where(['event_id' => '[0-9]+']);

Route::get('/events/{event_id}/tickets', 'API\TicketsController@getEventTickets')
        ->where(['event_id' => '[0-9]+']);

Route::get('/events/{event_id}/tickets/{type}', 'API\TicketsController@getEventTicketsByType')
    ->where(['event_id' => '[0-9]+']);


Route::get('/tickets/{ticket_id}', 'API\TicketsController@getTicket');

Route::post('/tickets/{ticket_id}/update', 'API\TicketsController@updateTicket');


Route::get('/redeem/{ticket_id}', 'API\TicketsController@checkTicket');