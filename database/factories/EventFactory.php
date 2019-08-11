<?php

/* @var $factory \Illuminate\Database\Eloquent\Factory */

use App\Model;
use App\Events;
use Faker\Generator as Faker;

$factory->define(Events::class, function (Faker $faker) {
    return [
        'event_name' => $faker->name,
        'description' => $faker->text,
        'category' => $faker->name,
        'location' => $faker->name, // password
        'event_time' => $faker->time,
        'event_date' => now(),
        'email' => $faker->unique()->safeEmail,
        'user_id' => $faker->numberBetween(1, 2)
    ];
});
