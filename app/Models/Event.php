<?php

namespace App\Models;

use App\Models\Ticket;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    protected $table = 'events';
    protected $primaryKey = 'event_id';
    public $timestamps = false;
    protected $fillable = ['name', 'event_date'];
    public $appends = ['number_of_tickets', 'number_of_redeemed', 'number_of_ok'];

    public function tickets(){
        return $this->hasMany(Ticket::class, 'event_id', 'event_id');
    }
    public function getNumberOfTicketsAttribute(){
        return $this->tickets()->count();
    }

    public function getNumberOfRedeemedAttribute(){
        return $this->tickets()->where('status', 0)->count();
    }
    public function getNumberOfOKAttribute(){
        return $this->tickets()->where('status', 1)->count();
    }
}
