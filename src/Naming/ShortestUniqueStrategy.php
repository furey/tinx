<?php

namespace Ajthinking\Tinx\Naming;

class ShortestUniqueStrategy implements Strategy
{
    /**
     * @param \Illuminate\Support\Collection $models
     * @return void
     * */
    public function __construct($models)
    {
        $this->models = $models;
    }

    /**
     * @return array
     * */
    public function getNames()
    {
        $names = [];

        foreach ($this->models as $model) {
            for ($i = 1; $i <= strlen($model->shortClassNameSlug); $i++) {
                $name = substr($model->shortClassNameSlug, 0, $i);
                if ($this->isValid($name)) {
                    $names[$model->fullClassName] = $name;
                    break;
                }
            }
        }

        return $names;
    }

    /**
     * @param string $name
     * @return bool
     * */
    private function isValid($name)
    {
        return false === (
            $this->hasSeveralLike($name) ||
            function_exists($name) ||
            ForbiddenNames::exists($name)
        );
    }

    /**
     * @param string $name
     * @return bool
     * */
    private function hasSeveralLike($name)
    {
        $matches = 0;

        foreach ($this->models as $model) {
            if ($this->startsWith($model->shortClassNameSlug, $name)) {
                $matches++;
            }
        }

        return $matches > 1;
    }

    /**
     * @param string $haystack
     * @param string $needle
     * @return bool
     * */
    private function startsWith($haystack, $needle)
    {
        return (substr($haystack, 0, strlen($needle)) === $needle);
    }
}
