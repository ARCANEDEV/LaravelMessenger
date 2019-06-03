<?php namespace Arcanedev\LaravelMessenger\Bases;

use Arcanedev\Support\Database\Migration as BaseMigration;

/**
 * Class     Migration
 *
 * @package  Arcanedev\LaravelMessenger\Bases
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
abstract class Migration extends BaseMigration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * Migration constructor.
     */
    public function __construct()
    {
        $this->setConnection(config('messenger.database.connection'));
        $this->setPrefix(config('messenger.database.prefix'));
    }
}
