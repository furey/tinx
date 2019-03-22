<?php

namespace Ajthinking\Tinx\Console;

use Ajthinking\Tinx\Console\NamesTable;
use Ajthinking\Tinx\Console\State;
use Ajthinking\Tinx\Includes\IncludeManager;
use Ajthinking\Tinx\Naming\StrategyFactory;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class TinxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tinx
                            {include?* : Include file(s) before starting tinker}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inject cool stuff into tinker';

    /**
     * @var array
     * */
    private $names;

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Tinx – something awesome is about to happen.");

        $this->listenForNamesTable();

        do {
            State::reset();
            $this->rebootConfig();
            $this->setNames();
            $this->createTinxIncludes();
            $this->callTinker();
        } while (State::shouldRestart() && !$this->info("Reloading your tinker session..."));

        State::reset();
    }

    /**
     * @return void
     * */
    private function rebootConfig()
    {
        app('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($this->laravel);
    }

    /**
     * @return void
     * */
    private function setNames()
    {
        $this->names = StrategyFactory::makeDefault()->getNames();
    }

    /**
     * @return void
     * */
    private function createTinxIncludes()
    {
        with(new IncludeManager)->generateIncludesFile($this->names);
    }

    /**
     * @return void
     * */
    private function listenForNamesTable()
    {
        app('events')->listen('tinx.names', function (...$args) {
            NamesTable::make($this)->render(...$args);
        });

        app('events')->listen('tinx.names.conditional', function (...$args) {
            NamesTable::make($this)->conditionallyRender();
        });
    }

    /**
     * @return void
     * */
    private function callTinker()
    {
        Artisan::call('tinker', ['include' => $this->getTinkerIncludes()]);
    }

    /**
     * @return array
     * */
    private function getTinkerIncludes()
    {
        /**
         * Magic functions and variables.
         * */
        $tinxIncludes = [
            $this->getStoragePath('includes.php'),
        ];

        /**
         * Files included by the user as command argument(s).
         * Example: "php artisan tinx include-1.php /path/to/include-2.php etc.php"
         * */
        $commandIncludes = $this->argument('include') ?: [];

        /**
         * Files included by the user via config.
         * */
        $configIncludes = config('tinx.include', []);

        return array_merge($tinxIncludes, $commandIncludes, $configIncludes);
    }

    /**
     * In Laravel 5.5 we'd simply "app('tinx.storage')->path('includes.php')",
     * but to support older versions (e.g. L5.3), we'll manually implement the
     * L5.5 Illuminate\Filesystem\FilesystemAdapter "path" method ourselves.
     *
     * @param string $path
     * @return string
     * */
    private function getStoragePath($path)
    {
        return app('tinx.storage')->getDriver()->getAdapter()->getPathPrefix().'/'.$path;
    }
}
