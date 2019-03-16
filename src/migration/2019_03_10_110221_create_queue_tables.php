<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
            $connection = config('queue.failed.database');
            if (!Schema::connection($connection)->hasTable(config('queue.failed.table'))) {
                Schema::connection($connection)->create(
                    config('queue.failed.table'),
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
            $connection = config('queue.failed.database');
            Schema::connection($connection)->dropIfExists(config('queue.connections.database.table'));
            Schema::connection($connection)->dropIfExists(config('queue.failed.table'));
        }
    }
}
