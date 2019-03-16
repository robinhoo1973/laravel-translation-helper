<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateQueueTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (config('queue.default') == 'database') {
            $connection = config('queue.database.failed.database');
            if (!Schema::connection($connection)->hasTable(config('queue.database.failed.table'))) {
                Schema::connection($connection)->create(
                    config('queue.database.failed.table'),
                    function (Blueprint $table) {
                        $table->bigIncrements('id');
                        $table->text('connection');
                        $table->text('queue');
                        $table->longText('payload');
                        $table->longText('exception');
                        $table->timestamp('failed_at')->useCurrent();
                    }
                );
            }

            if (!Schema::connection($connection)->hasTable(config('queue.connections.database.table'))) {
                Schema::connection($connection)->create(
                    config('queue.connections.database.table'),
                    function (Blueprint $table) {
                        $table->bigIncrements('id');
                        $table->string('queue')->index();
                        $table->longText('payload');
                        $table->unsignedTinyInteger('attempts');
                        $table->unsignedInteger('reserved_at')->nullable();
                        $table->unsignedInteger('available_at');
                        $table->unsignedInteger('created_at');
                    }
                );
            }
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        if (config('queue.default') == 'database') {
            $connection = config('queue.database.failed.database');
            Schema::connection($connection)->dropIfExists(config('queue.connections.database.table'));
            Schema::connection($connection)->dropIfExists(config('queue.database.failed.table'));
        }
    }
}