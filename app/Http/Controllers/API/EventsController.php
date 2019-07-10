<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TicketResource;
use App\Models\Event;
use App\Models\Ticket;
use App\Http\Resources\EventCollection ;
use App\Http\Resources\EventResource ;

use App\Http\Requests\EventRequest;

use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Response;

class EventsController extends BaseController
{
    /**
     * Return all events per page
     * use sort by parameter
     * @param Request $request
     * @return EventCollection|\Illuminate\Http\JsonResponse
     */
    public function getAll(Request $request){
        //return response()->json(new EventCollection(Event::get()), 200);
        // return new EventCollection(Event::get());
        try{
            $limit = ($request->input('per_page'))?? 10;
            $sortRules = ($request->input('sort')) ?? 'name|asc';
            //dd($limit);
            list($field, $dir) = explode('|', $sortRules);

            $event = Event::orderBy($field, $dir)->paginate($limit);
            return new EventCollection($event);
            //return response()->json($response, 200);
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Return target event with event_id
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getEvent($event_id){
        try{
            $event= Event::findOrFail($event_id);
            $response = new EventResource($event);
            return response()->json($response, 200);
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Create new Event with initial number of tickets
     * @param EventRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function addNew(EventRequest $request){
        try{
            $event = new Event();
            $event->name = $request->input('name');
            $event->event_date = $request->input('event_date');
            $event->save();
            if(intval($request->input('tickets')) > 0){
                for($i=0; $i< intval($request->input('tickets')); $i++){
                    $ticket = new Ticket();
                    $ticket->status = 1;
                    $event->tickets()->save($ticket);
                }
            }
            $response = new EventResource($event);
            return response()->json($response, 200);

        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update a target event
     *
     * @param EventRequest $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(EventRequest $request, $event_id){
        try{
            $event = $event= Event::findOrFail($event_id);
            $event->name = $request->input('name');
            $event->event_date = $request->input('event_date');
            $event->save();
            $response = new EventResource($event);
            return response()->json($response, 200);

        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Add new multiple of tickets related with target event
     * @param Request $request
     * @param $event_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function addTickets(Request $request, $event_id){
        try{
            $tickets_list=[];
            $event = $event= Event::findOrFail($event_id);
            if(intval($request->input('tickets')) > 0){
                for($i=0; $i< intval($request->input('tickets')); $i++){
                    $ticket = new Ticket();
                    $ticket->status = 1;
                    $event->tickets()->save($ticket);

                    $item = new TicketResource($ticket);
                    $tickets_list[]=$item;
                }
            }

            return response()->json($tickets_list, 200);

        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }
}
