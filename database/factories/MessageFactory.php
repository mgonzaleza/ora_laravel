<?php
$factory->define(App\Models\Message::class, function(Faker\Generator $faker) {
    return [
        'message' => $faker->sentence(5)
    ];
});
