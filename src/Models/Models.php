<?php

namespace Ajthinking\Tinx\Models;

use Exception;
use Illuminate\Support\Collection;

class Models extends Collection
{
    /**
     * @param mixed $items
     * @return void
     */
    public function __construct($items = [])
    {
        $this->filesystem = app('files');
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
        $modelFilePaths = config('tinx.model_paths', [
            '/app',
            '/app/Models/*',
        ]);

        $models = collect();

        foreach ($modelFilePaths as $modelFilePath) {
            $absoluteFilePath = $this->getAbsoluteFilePath($modelFilePath);
            $recursive = false;
            if (ends_with($absoluteFilePath, '*')) {
                $absoluteFilePath = rtrim($absoluteFilePath, '/*');
                $recursive = true;
            }
            if (false === file_exists($absoluteFilePath)) {
                continue;
            }
            foreach ($this->getVisibleFiles($absoluteFilePath, $recursive) as $filePath) {
                $fullClassName = $this->getFullClassName($filePath);
                if ($this->shouldNotInclude($filePath, $fullClassName)) {
                    continue;
                }
                $models->push(new Model($fullClassName));
            }
        }

        return $models;
    }

    /**
     * @param string $filePath
     * @return string
     * */
    private function getAbsoluteFilePath($path)
    {
        return base_path(trim($path, '/'));
    }

    /**
     * @param string $path
     * @param bool $recursive
     * @return array
     */
    private function getVisibleFiles($path, $recursive = false)
    {
        $method = $recursive ? 'allFiles' : 'files';

        return collect($this->filesystem->$method($path))->map(function ($file) {
            return is_string($file) ? $file : $file->getRealPath();
        })->values();
    }

    /**
     * @param string $path
     * @return $string
     * */
    private function getFullClassName($path)
    {
        $matches = [];

        try {
            preg_match($this->getNamespaceRegex(), $this->filesystem->get($path), $matches);
        } catch (Exception $e) {
            // Fail silently…
        }

        $namespace = array_get($matches, 1);

        if (null === $namespace) {
            return null;
        }

        return $namespace.'\\'.$this->filesystem->name($path);
    }

    /**
     * @return string
     * */
    private function getNamespaceRegex()
    {
        $start = $end = '/';
        $wordBoundary = '\b';
        $oneOrMoreSpaces = '\s+';
        $oneOrMoreWordsOrSlashes = '[\w|\\\]+';
        $zeroOrMoreSpaces = '\s*';
        $startGroup = '(';
        $endGroup = ')';
        $ignoreCase = 'i';

        return
            $start.
            $wordBoundary.'namespace'.$wordBoundary.$oneOrMoreSpaces.
            $startGroup.$oneOrMoreWordsOrSlashes.$endGroup.
            $zeroOrMoreSpaces.
            ';'.
            $end.
            $ignoreCase;
    }

    /**
     * @param string $filePath
     * @param string $fullClassName
     * @return bool
     * */
    private function shouldNotInclude($filePath, $fullClassName)
    {
        if (null === $fullClassName) {
            return true;
        }

        if ($this->except && in_array($fullClassName, $this->except)) {
            return true;
        }

        if ($this->only && false === in_array($fullClassName, $this->only)) {
            return true;
        }

        if (ModelValidator::make($filePath, $fullClassName)->fails()) {
            return true;
        }

        return false;
    }

    /**
     * Added for Laravel 5.2 backwards compatibility.
     *
     * @return \Illuminate\Support\Collection
     */
    public function toBase()
    {
        return new Collection($this);
    }
}
