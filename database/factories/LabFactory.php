<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Lab;
use Faker\Generator as Faker;
use Carbon\Carbon;

$factory->define(Lab::class, function (Faker $faker) {
    return [
        'code' => $faker->word,
        'content' => json_encode($faker->words(5)),
        'created_at'=> Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 years', '+1 month')->getTimestamp()),
        'updated_at'=> Carbon::createFromTimeStamp($faker->dateTimeBetween('-1 years', '+1 month')->getTimestamp()),     
    ];
});
