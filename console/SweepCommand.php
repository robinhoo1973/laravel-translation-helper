<?php
namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;

class SweepCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'trans-helper:sweep';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Verfiy existing the translation terms, remove unused cited or terms';
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
        sweep();
    }
}
