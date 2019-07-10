<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            "event_id"=> $this->event_id
            , "name" => $this->name
            , "event_date" => $this->event_date

            , "number_of_tickets" => $this->number_of_tickets
            , "number_of_redeemed" => $this->number_of_redeemed
            , "number_of_ok" => $this->number_of_ok
            , "updated_at" => $this->updated_at
            , "created_at" => $this->created_at
//            , "updated_at" =>Carbon::parse($this->updated_at)->format('d/m/Y H:i:s')
//            , "created_at" =>Carbon::parse($this->created_at)->format('d/m/Y H:i:s')
        ];
    }
}
