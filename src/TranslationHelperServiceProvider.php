<?php

namespace TopviewDigital\TranslationHelper;

use Illuminate\Support\ServiceProvider;

class TranslationHelperServiceProvider extends ServiceProvider
{
    /**
     * @var array
     */
    protected $commands = [
        Console\InstallCommand::class,
        Console\PublishCommand::class,
        Console\SweepCommand::class,
    ];

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->commands($this->commands);
    }

    private function publishAssets()
    {
        $this->publishes(
            [
                dirname(__DIR__).'/config/trans-helper.php' => config_path('trans-helper.php'),
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/migration');
        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }
}
