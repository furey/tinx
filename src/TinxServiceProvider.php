<?php

namespace Ajthinking\Tinx;


use Illuminate\Support\ServiceProvider;
use Ajthinking\Tinx\Commands\TinxCommand;

class TinxServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/tinx.php' => config_path('tinx.php'),
        ], 'config');

        $this->commands([
            TinxCommand::class
        ]);
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tinx.php', 'tinx');
    }
}
