<?php

declare(strict_types=1);

use Arcanedev\LaravelMessenger\Bases\Migration;
use Illuminate\Database\Schema\Blueprint;

/**
 * Class     CreateDiscussionsTable
 *
 * @author   ARCANEDEV <arcanedev.maroc@gmail.com>
 *
 * @see \Arcanedev\LaravelMessenger\Models\Discussion
 */
class CreateDiscussionsTable extends Migration
{
    /* -----------------------------------------------------------------
     |  Constructor
     | -----------------------------------------------------------------
     */

    /**
     * CreateDiscussionsTable constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->setTable(
            (string) config('messenger.discussions.table', 'discussions')
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
            $table->string('subject')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }
}
