<?php

use Arcanedev\LaravelMessenger\Models;
use Arcanedev\LaravelMessenger\Tests\Stubs\Models\User;
use Faker\Generator as Faker;

/** @var \Illuminate\Database\Eloquent\Factory  $factory */
$factory->define(Models\Discussion::class, function (Faker $f) {
    return [
        'subject' => 'Random discussion',
    ];
});

$factory->define(Models\Message::class, function (Faker $f) {
    return [
        'discussion_id' => function () { return factory(Models\Discussion::class)->create()->id; },
        'user_id'       => function () { return factory(User::class)->create()->id; },
        'body'          => $f->sentence,
    ];
});

$factory->define(Models\Participant::class, function (Faker $f) {
    return [
        'discussion_id' => function () { return factory(Models\Discussion::class)->create()->id; },
        'user_id'       => function () { return factory(User::class)->create()->id; },
    ];
});

$factory->define(User::class, function (Faker $f) {
    return [
        'name'  => $f->name,
        'email' => $f->safeEmail,
    ];
});
