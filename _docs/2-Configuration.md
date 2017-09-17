# 2. Configuration

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

After you've published the config file `config/laravel-messenger.php`, you can customize the settings :


```php
<?php

return [

    /* -----------------------------------------------------------------
     |  Database
     | -----------------------------------------------------------------
     */

    'database' => [
        'connection' => config('database.default'),
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

    'participants' => [
        'table' => 'participants',
        'model' => Arcanedev\LaravelMessenger\Models\Participant::class,
    ],

    'messages' => [
        'table' => 'messages',
        'model' => Arcanedev\LaravelMessenger\Models\Message::class
    ],

];
```
