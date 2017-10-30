<?php

namespace Ajthinking\Tinx;

use Exception;
use Illuminate\Support\Facades\File;

class ModelValidator
{
    /**
     * @param string $filePath
     * */
    public function __construct($filePath)
    {
        $this->filePath = $filePath;
    }

    /**
     * @param string $filePath
     * @return static
     * */
    public static function for($filePath)
    {
        return new static($filePath);
    }

    /**
     * @return bool
     * */
    public function fails()
    {
        return ! $this->passes();
    }

    /**
     * @return bool
     * */
    public function passes()
    {
        try {
            if ($this->isAbstractClass()) {
                return false;
            }
        } catch (Exception $e) {
            return false;
        }

        return true;
    }

    /**
     * @return bool
     * */
    private function isAbstractClass()
    {
        return preg_match($this->getAbstractClassRegex(), $this->getFileContents());
    }

    /**
     * Matches "abstract class ClassName {" (and similar variations).
     *
     * @return string
     * */
    private function getAbstractClassRegex()
    {
        $start = $end = '/';
        $wordBoundary = '\b';
        $oneOrMoreSpaces = '\s+';
        $oneOrMoreWordsOrSpaces = '[\w|\s]+';
        $ignoreCase = 'i';

        return
            $start.
            $wordBoundary.'abstract'.$wordBoundary.$oneOrMoreSpaces.
            $wordBoundary.'class'.$wordBoundary.$oneOrMoreWordsOrSpaces.
            '{'.
            $end.
            $ignoreCase;
    }

    /**
     * @return string
     * */
    private function getFileContents()
    {
        return File::get($this->filePath);
    }
}
