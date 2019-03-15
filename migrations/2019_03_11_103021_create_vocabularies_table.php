<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Migration auto-generated by Sequel Pro Laravel Export (1.5.0).
 *
 * @see https://github.com/cviebrock/sequel-pro-laravel-export
 */
class CreateVocabulariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        $connection = config('trans-helper.database.connection') ?: config('database.default');
        Schema::connection($connection)->create(config('trans-helper.database.table.cites'), function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('file', 256)->default('');
            $table->unsignedBigInteger('line');
            $table->string('class', 256)->nullable();
            $table->string('function', 256)->nullable();
            $table->mediumText('code')->nullable();
            $table->timestamps();

            $table->index('created_at', 'created_at');
            $table->index('updated_at', 'updated_at');
            $table->index('function', 'function');
            $table->index('file', 'file');
            $table->index('line', 'line');
            $table->index('class', 'class');
        });
        Schema::connection($connection)->create(
            config('trans-helper.database.database.table.term'),
            function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('namespace', 256)->default('');
                $table->string('term', 256)->default('');
                $table->json('translation');
                $table->timestamps();

                $table->unique(['namespace', 'term'], 'unique_namespace_term');
                $table->index('namespace', 'namespace');
                $table->index('term', 'term');
                $table->index('created_at', 'created_at');
                $table->index('updated_at', 'updated_at');
            }
        );

        Schema::connection($connection)->create(
            config('trans-helper.database.database.table.link'),
            function (Blueprint $table) {
                $table->unsignedBigInteger('cited');
                $table->unsignedBigInteger('vocab');

                $table->primary(['cited', 'vocab']);
                $table->index('cited', 'cited');
                $table->index('vocab', 'vocab');
                $table->foreign('cited')
                    ->references('id')
                    ->on(config('trans-helper.database.database.table.cite'))
                    ->onDelete('cascade');
                $table->foreign('vocab')
                    ->references('id')
                    ->on(config('trans-helper.database.database.table.term'))
                    ->onDelete('cascade');
            }
        );
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        $connection = config('trans-helper.database.connection') ?: config('database.default');
        Schema::connection($connection)->dropIfExists(config('trans-helper.database.table.link'));
        Schema::connection($connection)->dropIfExists(config('trans-helper.database.table.cite'));
        Schema::connection($connection)->dropIfExists(config('trans-helper.database.table.term'));
    }
}
