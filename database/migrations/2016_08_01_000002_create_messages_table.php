<?php

use Arcanedev\LaravelMessenger\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateMessagesTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @see \Arcanedev\LaravelMessenger\Models\Message
 */
class CreateMessagesTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * CreateMessagesTable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(
            config('laravel-messenger.messages.table', 'messages')
        );
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->createSchema(function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('discussion_id');
            $table->morphs(config("laravel-messenger.users.morph", 'participable'));
            $table->text('body');
            $table->timestamps();
        });
    }
}
