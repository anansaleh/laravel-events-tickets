<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class TicketResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        //return parent::toArray($request);
        return [
            "ticket_id"=> $this->ticket_id
            , "event_id" => $this->event_id
            , "event_name" => $this->event_name
            , "event_date" => $this->event_date
            , "status" => $this->status
            , "updated_at" => $this->updated_at
            , "created_at" => $this->created_at
        ];
    }
}
