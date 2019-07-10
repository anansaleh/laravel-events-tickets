<?php

namespace App\Models;

use App\Models\Event;
use App\Models\Concerns\UsesUuid;
use Illuminate\Database\Eloquent\Model;

class Ticket extends Model
{
    use UsesUuid;

    protected $table = 'events_tickets';
    protected $primaryKey = 'ticket_id';
    public $timestamps = false;

    protected $fillable = ['event_id', 'status'];
    public $appends = ['event_name', 'event_date'];


    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id', 'event_id');
    }
    public function getEventNameAttribute(){
        return $this->event->name;
    }
    public function getEventDateAttribute(){
        return $this->event->event_date;
    }
}
