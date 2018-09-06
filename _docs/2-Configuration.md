# 2. Configuration

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

After you've published the config file `config/messenger.php`, you can customize the settings :


```php
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
```
