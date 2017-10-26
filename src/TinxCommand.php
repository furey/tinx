<?php

namespace Ajthinking\Tinx;

use Illuminate\Console\Command;
use Ajthinking\Tinx\Model;
use Ajthinking\Tinx\State;
use Ajthinking\Tinx\IncludeManager;
use Artisan;
use Symfony\Component\Console\Input\InputArgument;

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
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->info("Tinx - something awesome is about to happen.");

        do {
            State::reset();
            $this->rebootConfig();
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
    private function createTinxIncludes()
    {
        IncludeManager::prepare(Model::all());
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
        // Magic functions and variables.
        $tinxIncludes = [
            app('tinx.storage')->path('includes.php')
        ];

        // Files included by the user as command argument(s).
        // e.g. "php artisan tinx include-1.php /path/to/include-2.php etc.php"
        $userIncludes = $this->argument('include') ?: [];

        return array_merge($tinxIncludes, $userIncludes);
    }
}
