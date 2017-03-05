<?php
$factory->define(App\Models\Chat::class, function(Faker\Generator $faker) {
    return [
        'name' => $faker->name
    ];
});
