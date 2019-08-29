<?php

namespace Ajthinking\Tinx\Models;

use Illuminate\Support\Str;

class Model
{
    /**
     * @param string $fullClassName
     * @return void
     * */
    public function __construct($fullClassName)
    {
        $this->fullClassName = $fullClassName;
        $this->shortClassName = class_basename($this->fullClassName);
        $this->namespace = trim(preg_replace("/{$this->shortClassName}$/", '', $this->fullClassName), '\\');
        $this->shortClassNameSlug = Str::slug($this->shortClassName);
        $this->shortClassNameWords = $this->getWordsFromString($this->shortClassName);
    }

    /**
     * @param string $string
     * @return array
     * */
    private function getWordsFromString($string)
    {
        return preg_split('/_|(\d+)/u', Str::snake($string), -1, PREG_SPLIT_DELIM_CAPTURE | PREG_SPLIT_NO_EMPTY);
    }
}
