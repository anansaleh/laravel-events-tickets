<?php

namespace App\Http\Controllers\API;

use App\Http\Resources\TicketCollection;
use App\Http\Resources\TicketResource;
use App\Models\Ticket;
use Illuminate\Http\Request;
use App\Http\Controllers\API\BaseController;
use Illuminate\Http\Response;

class TicketsController extends BaseController
{
    /**
     * Return a list of ticket per page accept parameter page, sort field and filter field in URL
     * @param Request $request
     * @param int $event_id
     * @return TicketCollection|\Illuminate\Http\JsonResponse
     */
    public function getEventTickets(Request $request, int $event_id){
        try{

            $tickets = Ticket::where('event_id', $event_id);

            //Check Filters
            if($request->has('filter_status') && trim($request->input('filter_status'))!=''){
                $tickets = $tickets->where('status', $request->input('filter_status'));
            }
            if ($request->has('filter_ticket_id')&& $request->input('filter_ticket_id')){
                $tickets = $tickets->where('ticket_id', 'like', $request->input('filter_ticket_id')."%");
            }

            // Set Sort by (Order BY)
            $sortRules = ($request->input('sort')) ?? 'ticket_id|asc';
            list($field, $dir) = explode('|', $sortRules);
            $tickets= $tickets->orderBy($field, $dir);

            // Set Pagination
            $limit = ($request->input('per_page'))?? 10;
            $tickets= $tickets->paginate($limit);

            return new TicketCollection($tickets);

        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }


    /**
     * Return a list of ticket that belong for target event with target status
     * @param Request $request
     * @param int $event_id
     * @param string $type
     * @return TicketCollection|\Illuminate\Http\JsonResponse
     */
    public function getEventTicketsByType(Request $request, int $event_id, $type='all'){
        try{
            $tickets = Ticket::where('event_id', $event_id);
            switch ($type){
                case 'ok':
                    $tickets = $tickets->where('status',1)->get();
                    break;
                case 'redeemed':
                    $tickets = $tickets->where('status',0)->get();
                    break;
                default:
                    $tickets = $tickets->get();
            }
            return new TicketCollection($tickets);
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Return a target ticket
     * @param $ticket_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicket($ticket_id){
        try{
            $ticket= Ticket::findOrFail($ticket_id);
            $response = new TicketResource($ticket);
            return response()->json($response, 200);
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Check if the status of target ticket if it is OK or Redeemed
     * @param $ticket_id
     * @return \Illuminate\Http\JsonResponse|Response
     */
    public function checkTicket($ticket_id){
        try{
            $ticket= Ticket::findOrFail($ticket_id);
            $response = new Response();
            //$response->setContent((string)$ticket);
            $response->setContent($ticket->toJson());
            if($ticket->status){
                $response->setStatusCode(200, 'OK');
            }else{
                $response->setStatusCode(410, 'GONE');
            }
            return $response;
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }

    /**
     * Update status of ticket
     * 
     * @param Request $request
     * @param $ticket_id
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateTicket(Request $request, $ticket_id){
        // Check tickets
        try{
            $ticket= Ticket::findOrFail($ticket_id);
            $request->validate([
                'status' => 'required|boolean',
            ]);

            $ticket->status=$request->input('status');
            $ticket->save();
            $response = new TicketResource($ticket);
            return response()->json($response, 200);
        }catch (\Exception $e){
            return $this->returnError($e->getMessage());
        }
    }


}
