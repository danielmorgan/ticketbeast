<?php

use Carbon\Carbon;

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\User::class, function (Faker\Generator $faker) {
    static $password;

    return [
        'name'           => $faker->name,
        'email'          => $faker->unique()->safeEmail,
        'password'       => $password ?: $password = bcrypt('secret'),
        'remember_token' => str_random(10),
    ];
});


/**
 * Concert
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Concert::class, function (Faker\Generator $faker) {
    return [
        'title'                  => 'Example Band',
        'subtitle'               => 'with The Fake Openers',
        'date'                   => Carbon::parse('+2 weeks'),
        'ticket_price'           => 2000,
        'venue'                  => 'The Example Theatre',
        'venue_address'          => '123 Example Lane',
        'city'                   => 'Testville',
        'state'                  => 'ON',
        'zip'                    => '90210',
        'additional_information' => 'Sample additional information.',
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Concert::class, 'published', function (Faker\Generator $faker) {
    return [
        'published_at' => Carbon::parse('-1 week'),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Concert::class, 'unpublished', function (Faker\Generator $faker) {
    return [
        'published_at' => null,
    ];
});


/** Order */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Order::class, function (Faker\Generator $faker) {
    return [
        'amount'              => 5250,
        'email'               => 'test@example.com',
        'confirmation_number' => 'ORDERCONFIRMATION1234',
        'card_last_four'      => '1234',
    ];
});


/**
 * Ticket
 */

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    return [
        'concert_id' => function () {
            return factory(\App\Concert::class)->create()->id;
        },
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Ticket::class, 'reserved', function (Faker\Generator $faker) {
    return [
        'reserved_at' => Carbon::now(),
    ];
});
