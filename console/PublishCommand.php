<?php
namespace TopviewDigital\TranslationHelper\Console;

use Illuminate\Console\Command;

class PublishCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'trans-helper:publish {--force}';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = "publish translation helper's assets, configuration, config and migration files. If you want overwrite the existing files, you can add the `--force` option";
    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $force = $this->option('force');
        $options = ['--provider' => 'TopviewDigital\TranslationHelper\TranslationHelperServiceProvider'];
        if ($force == true) {
            $options['--force'] = true;
        }
        $this->call('vendor:publish', $options);
    }
}
