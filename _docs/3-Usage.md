# 3. Usage

## Table of contents

  1. [Installation and Setup](1-Installation-and-Setup.md)
  2. [Configuration](2-Configuration.md)
  3. [Usage](3-Usage.md)

After publishing the package files, you need to update first the `config/laravel-messenger.php` config file to reference your User Model.

Don't forget to create a `users` table if you do not have one already.

Now you can run the `php artisan migrate` command to your database.

And add the `Arcanedev\LaravelMessenger\Traits\Messagable` trait to your `User` model like this:

```php
<?php

namespace App;

use Arcanedev\LaravelMessenger\Traits\Messagable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable {
    use Messagable;
    
    //...
}
```

### API

Try to check the tests folder for more details about the usage: [click here](https://github.com/ARCANEDEV/LaravelMessenger/blob/master/tests)
 
#### Discussions

Contract/Interface: [Link](https://github.com/ARCANEDEV/LaravelMessenger/blob/master/src/Contracts/Discussion.php)

#### Participation

Contract/Interface: [Link](https://github.com/ARCANEDEV/LaravelMessenger/blob/master/src/Contracts/Participation.php)

#### Messages

Contract/Interface: [Link](https://github.com/ARCANEDEV/LaravelMessenger/blob/master/src/Contracts/Message.php)
