<?php

namespace Ajthinking\Tinx;
use Ajthinking\Tinx\NamingStrategy;

class IncludeManager
{
    public static function prepare($models)
    {
        $strategy = config('tinx.strategy');
        $names = NamingStrategy::$strategy($models);
        IncludeManager::prepareIncludesFile($names);
    }

    public static function prepareIncludesFile($names)
    {
        $template = file_get_contents(__DIR__ . "/Stubs/TinxIncludes.php");
        $FIRST_MODEL_INSTANCE_VARIABLES = "";
        $LAST_MODEL_INSTANCE_VARIABLES = "";
        $MODEL_FUNCTIONS = "";
        foreach($names as $class => $name)
        {
            $FIRST_MODEL_INSTANCE_VARIABLES .= '$' . $name . ' = ' . $class . "::first();" . "\n";
            $LAST_MODEL_INSTANCE_VARIABLES .= '$' . $name . '_ = ' . $class . "::latest()->first();" . "\n";
            $MODEL_FUNCTIONS                .= 'function ' . $name . '($input = null) { return getQueryInstance("'. $class .'", $input);}' . "\n";
        }

        $replacementPairs = [
            '$FIRST_MODEL_INSTANCE_VARIABLES$' => $FIRST_MODEL_INSTANCE_VARIABLES,
            '$LAST_MODEL_INSTANCE_VARIABLES$' => $LAST_MODEL_INSTANCE_VARIABLES,
            '$MODEL_FUNCTIONS$' => $MODEL_FUNCTIONS,
            '$TINX_NAMES$' => '$names = ' . var_export($names, true) . ';'
        ];
        $filledTemplate = IncludeManager::fill_template($replacementPairs, $template);
        $file = fopen("storage/TinxIncludes.php", "w") or die("Unable to open tinx include file!");
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
