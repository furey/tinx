<?php

namespace Ajthinking\Tinx\Naming;

use Illuminate\Support\Arr;

class PascalStrategy implements Strategy
{
    /**
     * @var bool
     * */
    private $debugging = false;

    /**
     * @var array
     * */
    private $names = [];

    /**
     * @param \Illuminate\Support\Collection $models
     * @return void
     * */
    public function __construct($models)
    {
        $this->models = $models;
        $this->setModelNames();
    }

    /**
     * @return array
     * */
    public function getNames()
    {
        return $this->names;
    }

    /**
     * @return void
     * */
    private function setModelNames()
    {
        $this->prepareModelsForNaming();

        $this->models->each(function ($model) {
            $this->setModelName($model);
        });

        $this->prepareNamesForReturn();
    }

    /**
     * @param \Ajthinking\Tinx\Model $model
     * @return void
     * */
    private function setModelName($model)
    {
        /**
         * Pascal name (pascal case first characters).
         *
         * Example: 'MyClass' = 'mc'.
         * */
        $this->dump("Getting [{$model->fullClassName}] pascal name…");
        $name = $this->getPascalName($model);
        if ($this->canSetName($name)) {
            $this->dump("Setting [{$model->fullClassName}] as [{$name}].");
            return $this->setName($name, $model);
        }

        /**
         * Shortest unique pascal name.
         *
         * Example: 'MyClass', 'MyCar' = 'mcl', 'mc'.
         *
         * Note:    As 'Ca' is alphabetically before 'Cl', 'MyCar' gets the shorter abbreviation.
         * */
        $conflictingModel = $this->getConflictingModel($name, $model);
        $this->dump("Getting [{$model->fullClassName}] shortest unique pascal name…");
        $name = $this->getUniquePascalName($conflictingModel, $model);
        if ($this->canSetName($name)) {
            $this->dump("Setting [{$model->fullClassName}] as [{$name}].");
            return $this->setName($name, $model);
        }

        /**
         * Shortest unique namespaced pascal name.
         *
         * Example: 'App\A', 'App\Nested\A', 'App\Tested\A' = 'a', 'na', 'ta'.
         * */
        $conflictingModel = $this->getConflictingModel($name, $model);
        $this->dump("Getting [{$model->fullClassName}] shortest unique pascal namespaced name…");
        $name = $this->getUniquePascalNameWithNamespace($name, $conflictingModel, $model);
        if ($this->canSetName($name)) {
            $this->dump("Setting [{$model->fullClassName}] as [{$name}].");
            return $this->setName($name, $model);
        }

        /**
         * As a last resort, snake the full class name. "…It's something." ¯\_(ツ)_/¯
         * */
        $conflictingModel = $this->getConflictingModel($name, $model);
        $this->dump("Getting [{$model->fullClassName}] snake name…");
        $name = $this->getSnakeName($model);
        if ($this->canSetName($name)) {
            $this->dump("~~~☻ Snaking [{$model->fullClassName}] as [{$name}]. …It's something. ¯\_(ツ)_/¯");
            return $this->setName($name, $model);
        }

        $this->dump("Unable to set name for [{$model->fullClassName}].");
    }

    /**
     * @param \Ajthinking\Tinx\Model $model
     * @return string
     * */
    private function getPascalName($model)
    {
        return $this->getFirstCharacters($model->shortClassNameWords);
    }

    /**
     * @param string $conflictingName
     * @param \Ajthinking\Tinx\Model $causalModel
     * @return \Ajthinking\Tinx\Model
     * */
    private function getConflictingModel($conflictingName, $causalModel)
    {
        if ($this->isForbiddenName($conflictingName)) {
            $conflictingModel = $causalModel;
            $this->dump("Can't set [{$causalModel->fullClassName}] due to forbidden name [$conflictingName].");
        } else {
            $conflictingModel = Arr::get($this->names, $conflictingName, $causalModel);
            $this->dump("Can't set [{$causalModel->fullClassName}] as [$conflictingName] due to [{$conflictingModel->fullClassName}].");
        }

        return $conflictingModel;
    }

    /**
     * @param string $name
     * @return bool
     * */
    private function canSetName($name)
    {
        return false === $this->hasNameConflict($name);
    }

    /**
     * @param string $name
     * @return bool
     * */
    private function hasNameConflict($name)
    {
        if ($this->isForbiddenName($name)) {
            return true;
        }

        if (isset($this->names[$name])) {
            return true;
        }

        return false;
    }

    /**
     * @param string $name
     * @param \Ajthinking\Tinx\Model $model
     * @return void
     * */
    private function setName($name, $model)
    {
        if ($this->isForbiddenName($name)) {
            $patch = $this->getPatchedForbiddenName($name);
            $this->dump("[{$name}] is forbidden! Patching as [{$patch}].");
            $name = $patch;
        }

        $this->names[$name] = $model;
    }

    /**
     * @param string $name
     * @return bool
     * */
    private function isForbiddenName($name)
    {
        return ForbiddenNames::exists($name) || function_exists($name);
    }

    /**
     * @param string $name
     * @return string
     * */
    private function getPatchedForbiddenName($name)
    {
        do {
            $name = "_$name";
        } while ($this->hasNameConflict($name));

        return $name;
    }

    /**
     * @param \Ajthinking\Tinx\Model $conflictingModel
     * @param \Ajthinking\Tinx\Model $causualModel
     * @return string
     * */
    private function getUniquePascalName($conflictingModel, $causalModel)
    {
        /**
         * Calculate the first differet character between both short class names.
         * */
        $differenceOffset = $this->getDifferenceOffset(
            $conflictingModel->shortClassName,
            $causalModel->shortClassName
        );

        /**
         * Get the first characters of the causal model's short class name as an array of 'words',
         * including the full word containing the offset calculated above.
         *
         * Example: If the difference offset for 'MyClass' and 'MyClick' was 4 ('i'),
         *          the words for the causal model 'MyClick' would be ['m', 'click'].
         * */
        $words = $this->getFirstCharactersAndOffsetWordAsWords(
            $causalModel->shortClassNameWords,
            $differenceOffset
        );

        /**
         * Calculate the shortest conflict-free version of those words converted to a 'name' string.
         *
         * Example: If 'MyClass' was already set as 'mc', and the 'MyClick' casual string was 'mclick',
         *          we could set 'MyClick's name as 'mcl' if that name doesn't already exist in 'names'.
         * */
        $length = 0;
        $maxLength = mb_strlen(implode('', $words), 'UTF-8');
        do {
            $length++;
            $name = array_reduce($words, function ($carry, $word) use ($length) {
                return $carry . $this->getCharacters($word, 0, $length);
            });
        } while ($this->hasNameConflict($name) && $length <= $maxLength);

        return $name;
    }

    /**
     * @param string $conflictingName
     * @param \Ajthinking\Tinx\Model $conflictingModel
     * @param \Ajthinking\Tinx\Model $causualModel
     * @return string
     * */
    private function getUniquePascalNameWithNamespace($conflictingName, $conflictingModel, $causalModel)
    {
        $name = $conflictingName;
        $pascalName = $this->getPascalName($causalModel);

        /**
         * Determine the difference between the conflicting and causal model namespaces.
         * */
        $conflictingNamespaceWords = explode('\\', $conflictingModel->namespace);
        $causalNamespaceWords = explode('\\', $causalModel->namespace);
        $differentNamespaceSegments = array_diff_assoc($causalNamespaceWords, $conflictingNamespaceWords);
        if (!$differentNamespaceSegments) {
            $differentNamespaceSegments = $causalNamespaceWords;
        }

        /**
         * Get the first characters of the namespace differences as an array of 'words',
         * including the full last word.
         * */
        $differentWords = $this->splitPascalString(implode('', $differentNamespaceSegments));
        $differentWordsString = implode('', $differentWords);
        $words = $this->getFirstCharactersAndOffsetWordAsWords(
            $differentWords,
            $this->getLength($differentWordsString) - 1
        );

        /**
         * Calculate the shortest conflict-free version of those words plus the causal model pascal name,
         * converted to a 'name' string.
         * */
        $length = 0;
        $maxLength = mb_strlen(implode('', $words), 'UTF-8');
        do {
            $length++;
            $namespacePascalName = array_reduce($words, function ($carry, $word) use ($length) {
                return $carry . $this->getCharacters($word, 0, $length);
            });
            $name = $namespacePascalName . $pascalName;
        } while ($this->hasNameConflict($name) && $length <= $maxLength);

        return $name;
    }

    /**
     * @param \Ajthinking\Tinx\Model $model
     * @return string
     * */
    private function getSnakeName($model)
    {
        return str_replace('\\', '_', snake_case($model->fullClassName));
    }

    /**
     * Splits a pascal cased string into an array of lower case words (one or more adjacent numbers count as a word).
     *
     * @param string $string
     * @return array[string]
     * */
    private function splitPascalString($string)
    {
        return preg_split('/_|(\d+)/u', snake_case($string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }

    /**
     * @param array $words
     * @param int $offset
     * @return array[string]
     * */
    private function getFirstCharactersAndOffsetWordAsWords($words, $offset)
    {
        $rollingCount = 0;
        $offsetFound = false;
        return array_map(function ($word) use (&$rollingCount, &$offsetFound, $offset) {
            $wordLength = $this->getLength($word);
            $rollingCount += $wordLength;
            if (false === $offsetFound && $rollingCount >= $offset) {
                $offsetFound = true;
                return $word;
            }
            return $this->getFirstCharacter($word);
        }, $words);
    }

    /**
     * @param array $words
     * @return string
     * */
    private function getFirstCharacters($words)
    {
        return array_reduce($words, function ($carry, $word) {
            return $carry . $this->getFirstCharacter($word);
        });
    }

    /**
     * @param string $string
     * @return string
     * */
    private function getFirstCharacter($string)
    {
        return $this->getCharacterAt($string, 0);
    }

    /**
     * @param string $string
     * @param int $offset
     * @return string
     * */
    private function getCharacterAt($string, $offset)
    {
        return $this->getCharacters($string, $offset, 1);
    }

    /**
     * @param string $string
     * @param int $offset
     * @param int $length
     * @return string
     * */
    private function getCharacters($string, $offset, $length)
    {
        return mb_substr($string, $offset, $length, 'UTF-8');
    }

    /**
     * @param string $string1
     * @param string $string2
     * @return int
     * */
    private function getDifferenceOffset($string1, $string2)
    {
        return $this->getLength(mb_strcut($string1, 0, strspn($string1 ^ $string2, "\0"), 'UTF-8'));
    }

    /**
     * @param string $string
     * @return int
     * */
    private function getLength($string)
    {
        return mb_strlen($string, 'UTF-8');
    }

    /**
     * Sorts models so shallower FQCNs get naming rights over deeper FQCNs.
     *
     * @return void
     * */
    private function prepareModelsForNaming()
    {
        $this->models = $this->models
            ->sortBy('className')
            ->sort(function ($model1, $model2) {
                return $this->sortByNamespaceDepth($model1->fullClassName, $model2->fullClassName);
            })
            ->values();
    }

    /**
     * Flips 'names' array so FQCN is the key, then sorts alphabetically by namespace depth.
     *
     * @return void
     * */
    private function prepareNamesForReturn()
    {
        $this->names = array_flip(array_map(function ($model) {
            return $model->fullClassName;
        }, $this->names));

        ksort($this->names);

        uksort($this->names, [$this, 'sortByNamespaceDepth']);
    }

    /**
     * @param string $string1
     * @param string $string2
     * @return int
     * */
    private function sortByNamespaceDepth($string1, $string2)
    {
        $string1Count = substr_count($string1, '\\');
        $string2Count = substr_count($string2, '\\');

        if ($string1Count === $string2Count) {
            return 0;
        }

        return $string1Count < $string2Count ? -1 : 1;
    }

    /**
     * @param array $args
     * @return void
     * */
    private function dump(...$args)
    {
        if ($this->debugging) {
            dump(...$args);
        }
    }
}
