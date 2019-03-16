<?php

namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;

class ExportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'trans-helper:export';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Export translated terms into language files';
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
        export();
    }
}
