<?php

namespace Ajthinking\Tinx\Console;

class NamesTable
{
    /**
     * @param \Ajthinking\Tinx\Console\TinxCommand $command
     * @return void
     * */
    public static function for(TinxCommand $command)
    {
        return new static($command);
    }

    /**
     * @param \Ajthinking\Tinx\Console\TinxCommand $command
     * @return void
     * */
    private function __construct(TinxCommand $command)
    {
        $this->command = $command;
        $this->names = $this->command->getNames();
        $this->headers = $this->getHeaders();
        $this->rows = $this->getRows();
    }

    /**
     * @return array
     * */
    private function getHeaders()
    {
        return ['Class', 'Shortcuts'];
    }

    /**
     * @return array
     * */
    private function getRows()
    {
        return collect($this->names)->map(function ($name, $class) {
            return [$class, "\${$name}, \${$name}_, {$name}()"];
        })->toArray();
    }

    /**
     * @return void
     * */
    public function conditionallyRender()
    {
        if ($this->shouldRender()) {
            $this->render();
        }
    }

    /**
     * @return void
     * */
    public function render()
    {
        if (0 === count($this->names)) {
            return $this->command->warn("No models found (see: config/tinx.php > namespaces_and_paths).");
        }

        $this->command->table($this->headers, $this->rows);
    }

    /**
     * @return bool
     * */
    private function shouldRender()
    {
        $totalNames = count($this->names);

        if ($this->command->option('verbose')) {
            return true;
        }

        if (0 === $totalNames) {
            return false;
        }

        $namesTableLimit = (int) config('tinx.names_table_limit', 10);

        if ($namesTableLimit === -1) {
            return true;
        }

        if ($totalNames <= $namesTableLimit) {
            return true;
        }

        return false;
    }
}
