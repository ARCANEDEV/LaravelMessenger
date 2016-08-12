<?php

use Arcanedev\LaravelMessenger\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateParticipantsTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 */
class CreateParticipantsTable extends Migration
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
            $this->getTableFromConfig('participants', 'participants')
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
            $table->dateTime('last_read')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
