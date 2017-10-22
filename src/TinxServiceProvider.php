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
        //
    }
}
