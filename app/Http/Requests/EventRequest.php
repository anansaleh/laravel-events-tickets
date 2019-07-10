<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class EventRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        if($this->has('event_id')){
            return $this->rulesWithEventID();
        }else{
            return $this->rulesWithoutEventID();
        }
    }
    private function rulesWithEventID(){
        return [
            "event_id"=>'required:exists:events,event_id'
            , "name" => "required|min:8"
            , "event_date" => "required|date"
        ];
    }
    private function rulesWithoutEventID(){
        return [
            "name" => "required|min:8"
            , "event_date" => "required|date"
            , "tickets" => "nullable|numeric|min:0"
        ];
    }
    public function messages()
    {
        return [
            'name.required' => 'Name is required!',
            'event_date.required' => 'Event Date is required!',
            'event_date.date' => "Event Date must be date format 'Y-m-d H:i:s' ",
            'tickets' => 'Tickets must be number > 0 or null'
        ];
    }
}
