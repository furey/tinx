<?php

namespace Ajthinking\Tinx;

class NamingStrategy
{
    public static function shortestUnique($models) {
        $modelCollection = new ModelCollection($models);
        $names = [];
        //dd($models);
        foreach($models as $model)
        {
            for ($i = 1; $i <= strlen($model->slug); $i++) {                
                $nameCandidate = substr($model->slug, 0, $i);                                
                if(!$modelCollection->hasSeveralLike($nameCandidate)  && !function_exists($nameCandidate) && $nameCandidate != "names" && $nameCandidate != "re") {                    
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