<?php

namespace Ajthinking\Tinx;

use Ajthinking\Tinx\Console\TinxCommand;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

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

        $this->ignoreStorageFiles();

        $this->setViewPaths();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/tinx.php', 'tinx');

        $this->configureStorageDisk();
    }

    /**
     * @return void
     * */
    private function configureStorageDisk()
    {
        config([
            'filesystems.disks.tinx' => config('filesystems.disks.tinx', [
                'driver' => 'local',
                'root' => storage_path('tinx'),
            ]),
        ]);

        $this->app->singleton('tinx.storage', function ($app) {
            return $app['filesystem']->disk('tinx');
        });
    }

    /**
     * @return void
     * */
    private function ignoreStorageFiles()
    {
        if (!app('tinx.storage')->exists('.gitignore')) {
            app('tinx.storage')->put('.gitignore', '*'.PHP_EOL.'!.gitignore');
        }
    }

    /**
     * @return void
     * */
    private function setViewPaths()
    {
        $viewPath = __DIR__.'/../resources/views';

        $viewFactory = $this->app['view'];
        $viewFactory->addLocation($viewPath);
        $viewFactory->addNamespace('tinx', $viewPath);
    }
}
