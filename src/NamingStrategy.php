<?php

namespace Ajthinking\Tinx;

class NamingStrategy
{
    const forbiddenNames = [
        // reserved keywords
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case', 'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do', 'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif', 'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach', 'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once', 'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or', 'print', 'private', 'protected', 'public', 'require', 'require_once', 'return', 'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor',    
        // predefined_constants
        '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__',
        // used by tinx
        're', 'names', 'getQueryInstance'
    ];
    
    public static function shortestUnique($models) {
        $modelCollection = new ModelCollection($models);
        $names = [];
        foreach($models as $model)
        {
            for ($i = 1; $i <= strlen($model->slug); $i++) {
                $nameCandidate = substr($model->slug, 0, $i);
                if(!$modelCollection->hasSeveralLike($nameCandidate)  && !function_exists($nameCandidate) && !in_array($nameCandidate, NamingStrategy::forbiddenNames)) {
                    $names[$model->classWithFullNamespace] = $nameCandidate;
                    break;
                }
            }
        }
        return $names;
    }
}

class ModelCollection
{
   public function __construct($models) {
        $this->models = $models;
   }

   public function hasSeveralLike($nameCandidate)
   {
       $matches = 0;
       foreach($this->models as $model)
       {
           if($this->startsWith($model->slug, $nameCandidate))
           {
                $matches++;
           }
       }
       return $matches > 1;
   }

   private function startsWith($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }
}
