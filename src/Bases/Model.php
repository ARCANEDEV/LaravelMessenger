<?php namespace Arcanedev\LaravelMessenger\Bases;

use Arcanedev\LaravelMessenger\Traits\ConfigHelper;
use Arcanedev\Support\Bases\Model as BaseModel;

/**
 * Class     Model
 *
 * @package  Arcanedev\LaravelMessenger\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Model extends BaseModel
{
    /* ------------------------------------------------------------------------------------------------
     |  Traits
     | ------------------------------------------------------------------------------------------------
     */
    use ConfigHelper;

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
        $this->setConnection(
            $this->getFromConfig('database.connection')
        );

        parent::__construct($attributes);
    }
}
