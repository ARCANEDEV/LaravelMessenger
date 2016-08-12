<?php namespace Arcanedev\LaravelMessenger\Tests\Stubs\Models;

use Arcanedev\LaravelMessenger\Traits\Messagable;
use Arcanedev\Support\Bases\Model;

/**
 * Class     User
 *
 * @package  Arcanedev\LaravelMessenger\Tests\Models
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class User extends Model
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use Messagable;

    /* ------------------------------------------------------------------------------------------------
     |  Properties
     | ------------------------------------------------------------------------------------------------
     */
    protected $fillable = ['name', 'email'];
}
