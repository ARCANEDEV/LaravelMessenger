<?php

return [

    /* -----------------------------------------------------------------
     |  Database
     | -----------------------------------------------------------------
     */

    'database' => [
        'connection' => env('DB_CONNECTION', 'mysql'),
    ],

    /* -----------------------------------------------------------------
     |  Models
     | -----------------------------------------------------------------
     */

    'users' => [
        'table' => 'users',
        'model' => App\User::class,
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
