<?php

namespace Ajthinking\Tinx;

class IncludeManager
{
    public static function autoInclude($models) 
    {
        $template = file_get_contents(__DIR__ . "/Stubs/TinxAutoInclude.php");
        $FIRST_MODEL_INSTANCE_VARIABLES = "";
        foreach($models as $model)
        {   
            $FIRST_MODEL_INSTANCE_VARIABLES .= '$u = ' . $model->classWithFullNamespace . "::first();" . "\n";
        }
        
        $replacementPairs = [
                '$FIRST_MODEL_INSTANCE_VARIABLES$' => $FIRST_MODEL_INSTANCE_VARIABLES
        ];
        $filledTemplate = IncludeManager::fill_template($replacementPairs, $template); 
        $file = fopen("storage/TinxAutoInclude.php", "w") or die("Unable to open tinx include file!");
        fwrite($file, $filledTemplate);
        fclose($file);        
        return true;
    }
    
    public static function fill_template($variables, $template)
    {
        foreach ($variables as $variable => $value) {
            $template = str_replace($variable, $value, $template);
        }
        return $template;
    }     
}


