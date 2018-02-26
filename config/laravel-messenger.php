<?php

return [

    /* -----------------------------------------------------------------
     |  Database
     | -----------------------------------------------------------------
     */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),

        'prefix'     => null,
    ],

    /* -----------------------------------------------------------------
     |  Models
     | -----------------------------------------------------------------
     */

    'users' => [
        'table' => 'users',
        'model' => App\User::class,
        'morph' => 'participable',
    ],

    'discussions' => [
        'table' => 'discussions',
        'model' => Arcanedev\LaravelMessenger\Models\Discussion::class
    ],

    'participations' => [
        'table' => 'participations',
        'model' => Arcanedev\LaravelMessenger\Models\Participation::class,
    ],

    'messages' => [
        'table' => 'messages',
        'model' => Arcanedev\LaravelMessenger\Models\Message::class
    ],

];
