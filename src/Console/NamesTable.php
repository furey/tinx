<?php

namespace Ajthinking\Tinx\Console;

use Illuminate\Support\Arr;

class NamesTable
{
    /**
     * @param \Ajthinking\Tinx\Console\TinxCommand $command
     * @return void
     * */
    public static function make(TinxCommand $command)
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
        $this->names = Arr::get($GLOBALS, 'tinx.names');
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

        if (-1 === $namesTableLimit) {
            return true;
        }

        if ($totalNames <= $namesTableLimit) {
            return true;
        }

        $this->command->warn($this->getLimitWarning($totalNames));

        if ($totalNames > $namesTableLimit * 1.5) {
            $this->command->line($this->getSearchHint());
        }

        return false;
    }

    /**
     * @param array $filters
     * @return void
     * */
    public function render(...$filters)
    {
        if (0 === count($this->names)) {
            return $this->command->warn("No models found (see: config/tinx.php > model_paths).");
        }

        $rows = $this->getRows();

        if ($filters) {
            $rows = $this->filterRows($rows, $filters);
            if (0 === count($rows)) {
                return $this->command->warn($this->getFiltersWarning($filters));
            }
        }

        $this->command->table($this->getHeaders(), $rows->toArray());
    }

        /**
     * @return array
     * */
    private function getHeaders()
    {
        return ['Class', 'Shortcuts'];
    }

    /**
     * @return \Illuminate\Support\Collection
     * */
    private function getRows()
    {
        return collect($this->names)->map(function ($shortcuts, $class) {
            return [$class, $shortcuts];
        });
    }

    /**
     * @param \Illuminate\Support\Collection $rows
     * @param array $filters
     * @return \Illuminate\Support\Collection
     * */
    private function filterRows($rows, $filters = [])
    {
        $regex = '/'.implode('|', $filters).'/i';

        return $rows->filter(function ($row) use ($regex) {
            return preg_match($regex, $row[0]);
        });
    }

    /**
     * @param array $filters
     * @return string
     * */
    private function getFiltersWarning($filters)
    {
        return sprintf(
            'No class shortcuts found for search %s [%s].',
            str_plural('term', count($filters)),
            implode(', ', $filters)
        );
    }

    /**
     * @param string $totalNames
     * @return string
     * */
    private function getLimitWarning($totalNames)
    {
        return sprintf(
            '%d %s found (to view shortcuts on boot, see: config/tinx.php > names_table_limit).',
            $totalNames,
            str_plural('model', $totalNames)
        );
    }

    /**
     * @return string
     * */
    private function getSearchHint()
    {
        return 'Hint: Filter your model shortcuts by passing terms to "names()" e.g. "names(\'User\', \'Team\')" etc.';
    }
}
