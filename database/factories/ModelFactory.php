<?php

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


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Concert::class, function (Faker\Generator $faker) {
    return [
        'title'                  => 'Example Band',
        'subtitle'               => 'with The Fake Openers',
        'date'                   => Carbon\Carbon::parse('+2 weeks'),
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
        'published_at' => Carbon\Carbon::parse('-1 week'),
    ];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->state(App\Concert::class, 'unpublished', function (Faker\Generator $faker) {
    return [
        'published_at' => null,
    ];
});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Ticket::class, function (Faker\Generator $faker) {
    return [
        'concert_id' => function () {
            return factory(\App\Concert::class)->create()->id;
        },
    ];
});
