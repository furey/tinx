<?php

namespace Ajthinking\Tinx;

use Illuminate\Console\Command;
use Ajthinking\Tinx\Model;
use Ajthinking\Tinx\State;
use Ajthinking\Tinx\IncludeManager;
use Artisan;

class TinxCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tinx';

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
            IncludeManager::prepare(Model::all());
            Artisan::call('tinker', [
                'include' => [
                    app('tinx.storage')->path('includes.php')
                ]
            ]);
        } while (State::shouldRestart() && !$this->info("Reloading your tinker session."));

        State::reset();
    }

    /**
     * @return void
     * */
    private function rebootConfig()
    {
        app('Illuminate\Foundation\Bootstrap\LoadConfiguration')->bootstrap($this->laravel);
    }
}
