<?php

use App\Models\Event;
use App\Models\Ticket;
use Faker\Generator as Faker;


$factory->define(Event::class, function (Faker $faker) {
    return [
        'name' => $faker->sentence(),
        // 'event_date' => $faker->dateTime($max = '2019-12-31 21:00:00', $timezone=null),
        'event_date' => function(){
                    // get date
                    // Convert to timetamps
                    $min = time(); // current Unix timestamp
                    $max = strtotime('2019-12-31 20:00:00');
                    // Generate random number using above bounds
                    $val = rand($min, $max);
                    $date = date('Y-m-d', $val);

                    //get time:
                    // the day int_min & t_max is not important just to take the time
                    $t_min = strtotime('2019-01-01 09:00:00');
                    $t_max = strtotime('2019-01-01 21:00:00');
                    $val = rand($t_min, $t_max);
                    $time = date(' H:i:s', $val);
                    return ($date.$time);
                },
    ];
});
$factory->define(Ticket::class, function (Faker $faker) {
    return [
        'status' => random_int($min = 0, $max = 1)
    ];
});

