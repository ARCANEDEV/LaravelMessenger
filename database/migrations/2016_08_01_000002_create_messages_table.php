<?php

declare(strict_types=1);

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
            (string) config('messenger.messages.table', 'messages')
        );
    }

    /* -----------------------------------------------------------------
     |  Main Methods
     | -----------------------------------------------------------------
     */

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $this->createSchema(function (Blueprint $table): void {
            $table->increments('id');
            $table->unsignedInteger('discussion_id');
            $table->morphs(config('messenger.users.morph', 'participable'));
            $table->text('body');
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
