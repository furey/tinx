<?php

namespace Ajthinking\Tinx;

class Model
{
    public function __construct($classWithFullNamespace) {
        $this->classWithFullNamespace = $classWithFullNamespace;
        $parts = explode('\\',$classWithFullNamespace);
        $this->className = end($parts);
        $this->slug = str_slug($this->className);        
    }

    public static function all()
    {
        // This should be moved to a publishable config file
        $namespacesAndPaths = [
            "App" => "/app",
            "App\Models" => "/app/Models"
        ];

        $models = collect();

        foreach($namespacesAndPaths as $namespace => $path)
        {
            $fullBasePath = base_path() . $path;
            if(file_exists($fullBasePath))
            {
                $results = scandir($fullBasePath);
                foreach ($results as $result) {
                    if ($result === '.' or $result === '..') continue;
                    $filename = $fullBasePath . '/' . $result;            
                    if (is_dir($filename)) {
                        // This requires only model files to be present in subfolders, anything else will brake it.
                        //$models = array_merge($models, $this->models($filename));
                    }else{
                        $class = $namespace . '\\' . substr($result,0,-4);
                        $models->push(new Model($class));
                        
                    }
                }
            }
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
    public function publicAttributes() {        
        return array_values(
            array_diff(
                array_keys(
                    $this->sample()->getAttributes()
                ),
                $this->sample()->getHidden()
            )
        );
    }

    public function hasTable() {

    }    
}
