<?php

namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;

class TranslationCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'trans-helper:trans';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'TRanslation existing terms after sweep';
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
        translation();
    }
}
