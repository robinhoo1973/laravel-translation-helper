<?php

namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Schema;

class InstallCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'trans-helper:install';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Install the translation helper tables accoding to config';
    /**
     * Install directory.
     *
     * @var string
     */
    protected $directory = '';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $this->initDatabase();
    }

    /**
     * Create tables and seed it.
     *
     * @return void
     */
    public function initDatabase()
    {
        $connection = config('trans-helper.database.connection') ?: config('database.default');
        if (!Schema::connection($connection)->hasTable('jobs')) {
            $this->call('queue:table');
        }
        if (!Schema::connection($connection)->hasTable(config('trans-helper.database.table.cite'))) {
            $this->call('migrate');
        }
    }
}
