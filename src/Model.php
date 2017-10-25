<?php

namespace Ajthinking\Tinx;

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
                    if (is_dir($filename)) {
                        // Only model files may be present in subfolders; anything else will break it.
                    } else {
                        $class = $namespace . '\\' . substr($result, 0, -4);
                        if ((new \ReflectionClass($class))->isAbstract()) {
                            continue;
                        }
                        $models->push(new Model($class));

                    }
                }
            }
        }
        return $models;
    }

    /**
     * @param string $fullBasePath
     * @return array
     */
    private static function nonHiddenFiles($fullBasePath)
    {
        return preg_grep('/^([^.])/', scandir($fullBasePath));
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

    public function hasTable()
    {
        //
    }
}
