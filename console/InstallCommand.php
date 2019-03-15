<?php
namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;

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
        $this->call('migrate');
    }
}
