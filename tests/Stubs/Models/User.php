<?php

declare(strict_types=1);

namespace Arcanedev\LaravelMessenger\Tests\Stubs\Models;

use Arcanedev\LaravelMessenger\Traits\Messagable;
use Arcanedev\Support\Database\Model;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class User extends Model
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use Messagable;

    /* -----------------------------------------------------------------
     |  Properties
     | -----------------------------------------------------------------
     */

    protected $fillable = ['name', 'email'];

    protected $casts = [
        'id' => 'integer',
    ];
}
