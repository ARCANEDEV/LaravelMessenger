<?php

use Arcanedev\LaravelMessenger\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateMessagesTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateMessagesTable extends Migration
{
    /* ------------------------------------------------------------------------------------------------
     |  Constructor
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * CreateParticipantsTable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(
            $this->getTableFromConfig('messages', 'messages')
        );
    }

    /* ------------------------------------------------------------------------------------------------
     |  Main Functions
     | ------------------------------------------------------------------------------------------------
     */
    /**
     * Run the migrations.
     */
    public function up()
    {
        $this->createSchema(function (Blueprint $table) {
            $table->increments('id');
            $table->integer('discussion_id')->unsigned();
            $table->integer('user_id')->unsigned();
            $table->text('body');
            $table->timestamps();
        });
    }
}
