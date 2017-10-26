<?php

namespace Ajthinking\Tinx;

use Ajthinking\Tinx\NamingStrategy;

class IncludeManager
{
    public static function prepare($models)
    {
        $strategy = config('tinx.strategy', 'shortestUnique');
        $names = NamingStrategy::$strategy($models);
        IncludeManager::prepareIncludesFile($names);
    }

    /**
     * @param array $names
     * @return void
     * */
    public static function prepareIncludesFile($names)
    {
        $config = config('tinx');
        $contents = view()->file(__DIR__.'/resources/includes.blade.php', compact('names', 'config'))->render();
        $contentsWithPhpTag = '<?php'.PHP_EOL.PHP_EOL.$contents;
        app('tinx.storage')->put('includes.php', $contentsWithPhpTag);
    }
}
