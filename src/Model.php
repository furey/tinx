<?php

namespace Ajthinking\Tinx;

use Exception;
use ReflectionClass;

class Model
{
    public function __construct($classWithFullNamespace)
    {
        $this->classWithFullNamespace = $classWithFullNamespace;
        $parts                        = explode('\\', $classWithFullNamespace);
        $this->className              = end($parts);
        $this->slug                   = str_slug($this->className);
    }

    public static function all()
    {
        $namespacesAndPaths = config('tinx.namespaces_and_paths', [
            'App' => '/app',
            'App\Models' => '/app/Models',
        ]);

        $models = collect();

        foreach ($namespacesAndPaths as $namespace => $path) {
            $fullBasePath = base_path() . $path;
            if (file_exists($fullBasePath)) {
                $results = self::nonHiddenFiles($fullBasePath);
                foreach ($results as $result) {
                    $filename = $fullBasePath . '/' . $result;
                    // Only model files may be present in subfolders; anything else will break it.
                    if (!is_dir($filename)) {
                        $class = $namespace . '\\' . substr($result, 0, -4);
                        if (!self::validateClass($class)) {
                            continue;
                        }
                        $models->push(new Model($class));
                    }
                }
            }
        }

        return self::filterModels($models);
    }

    /**
     * @param string $fullBasePath
     * @return array
     */
    private static function nonHiddenFiles($fullBasePath)
    {
        return preg_grep('/^([^.|^~])/', scandir($fullBasePath));
    }

    /**
     * @param string $class
     * @return bool
     * */
    private static function validateClass($class)
    {
        try {
            if ((new ReflectionClass($class))->isAbstract()) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @param Collection $models
     * @return Collection
     * */
    private static function filterModels($models)
    {
        if (($only = config('tinx.only', [])) && is_array($only) && count($only)) {
            $models = $models->filter(function ($model) use ($only) {
                return in_array($model->classWithFullNamespace, $only);
            });
        }

        if (($except = config('tinx.except', [])) && is_array($except) && count($except)) {
            $models = $models->reject(function ($model) use ($except) {
                return in_array($model->classWithFullNamespace, $except);
            });
        }

        return $models;
    }

    public function empty()
    {
        return ! (boolean) $this->classWithFullNamespace::first();
    }

    public function sample()
    {
        return $this->classWithFullNamespace::first();
    }

    // attributes - hidden attributes as non associative array
    public function publicAttributes()
    {
        return array_values(
            array_diff(
                array_keys(
                    $this->sample()->getAttributes()
                ),
                $this->sample()->getHidden()
            )
        );
    }
}
