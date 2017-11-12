<?php

namespace Ajthinking\Tinx\Models;

use Illuminate\Support\Collection;

class Models extends Collection
{
    /**
     * @param mixed $items
     * @return void
     */
    public function __construct($items = [])
    {
        $this->only = $this->getOnlyArray();
        $this->except = $this->getExceptArray();

        parent::__construct($items ?: $this->getModels());
    }

    /**
     * @return array
     * */
    private function getOnlyArray()
    {
        return ($only = config('tinx.only')) && is_array($only) && count($only) ? $only : null;
    }

    /**
     * @return array
     * */
    private function getExceptArray()
    {
        return ($except = config('tinx.except')) && is_array($except) && count($except) ? $except : null;
    }

    /**
     * @return \Illuminate\Support\Collection
     * */
    private function getModels()
    {
        $namespacePaths = config('tinx.namespaces_and_paths', [
            'App' => '/app',
            'App\Models' => '/app/Models',
        ]);

        $models = collect();

        foreach ($namespacePaths as $namespace => $path) {
            $namespacePath = base_path($path);
            if (false === file_exists($namespacePath)) {
                continue;
            }
            foreach ($this->getVisibleFiles($namespacePath) as $file) {
                $fullClassName = $namespace.'\\'.substr($file, 0, -4);
                if ($this->isHidden($fullClassName)) {
                    continue;
                }
                $filePath = $namespacePath.'/'.$file;
                if (ModelValidator::for($filePath, $fullClassName)->fails()) {
                    continue;
                }
                $models->push(new Model($fullClassName));
            }
        }

        return $models;
    }

    /**
     * @param string $path
     * @return array
     */
    private function getVisibleFiles($path)
    {
        return preg_grep('/^([^.|^~])/', scandir($path));
    }

    /**
     * @param string $fullClassName
     * @return bool
     * */
    private function isHidden($fullClassName)
    {
        if ($this->except && in_array($fullClassName, $this->except)) {
            return true;
        }

        if ($this->only && false === in_array($fullClassName, $this->only)) {
            return true;
        }

        return false;
    }
}
