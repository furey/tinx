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

        $this->ignoreStorageFiles();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/tinx.php', 'tinx');

        $this->configureStorageDisk();
    }

    /**
     * @return void
     * */
    private function configureStorageDisk()
    {
        config([
            'filesystems.disks.tinx' => config('tinx.storage.disk', [
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
        resolve('tinx.storage')->put('.gitignore', '*'.PHP_EOL.'!.gitignore');
    }
}
