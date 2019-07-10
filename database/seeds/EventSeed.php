<?php

use App\Models\Event;
use App\Models\Ticket;
use Faker\Factory as Faker;
use Illuminate\Database\Seeder;

class EventSeed extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $faker = Faker::create();
        factory(Event::class, 30)->create()->each(function ($event) use ($faker){
            $max_tickets = random_int($min = 0, $max = 50);
            if($max_tickets > 0){
                $event->tickets()->saveMany(factory(Ticket::class, $max_tickets)->make());
            }
        });
    }
}
