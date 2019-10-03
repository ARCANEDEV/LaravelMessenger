<?php namespace Arcanedev\LaravelMessenger\Models;

use Arcanedev\Support\Database\Model as BaseModel;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelMessenger\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Model extends BaseModel
{
    /* -----------------------------------------------------------------
     |  Traits
     | -----------------------------------------------------------------
     */

    use SoftDeletes;

    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Create a new Eloquent model instance.
     *
     * @param  array  $attributes
     */
    public function __construct(array $attributes = [])
    {
        $this->setConnection(config('messenger.database.connection'));
        $this->setPrefix(config('messenger.database.prefix'));

        parent::__construct($attributes);
    }
}
