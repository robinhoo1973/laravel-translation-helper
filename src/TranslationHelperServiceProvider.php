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
        $this->loadAdminAuthConfig();
        $this->registerRouteMiddleware();
        $this->commands($this->commands);
        $this->publishAssets();
    }

    private function publishAssets()
    {
        $this->publishes(
            [dirname(__DIR__).'/config' => config_path()],
            'translation-helper-config'
        );
        $this->publishes(
            [__DIR__.'/../database/migrations' => database_path('migrations')],
            'translation-helper-migrations'
        );
    }

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishAssets();
        }
    }
}
