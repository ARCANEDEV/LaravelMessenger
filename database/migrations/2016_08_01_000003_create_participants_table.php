<?php

use Arcanedev\LaravelMessenger\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateParticipantsTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @see \Arcanedev\LaravelMessenger\Models\Participant
 */
class CreateParticipantsTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
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
            $table->unsignedInteger('user_id');
            $table->dateTime('last_read')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
