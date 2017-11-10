<?php

namespace Ajthinking\Tinx\Naming;

use Ajthinking\Tinx\Models\Models;
use Ajthinking\Tinx\Naming\NamingStrategy;
use Exception;

class StrategyFactory
{
    /**
     * @return \Ajthinking\Tinx\Naming\NamingStrategy
     * */
    public static function makeDefault()
    {
        $strategy = config('tinx.strategy', 'pascal');

        $models = new Models;

        return static::make($strategy, $models);
    }

    /**
     * Accepts a string identifier (e.g. 'pascal') or any class implementing 'Ajthinking\Tinx\Naming\NamingStrategy'.
     *
     * @param string $strategy
     * @return \Ajthinking\Tinx\Naming\NamingStrategy
     * */
    public static function make($strategy, $models)
    {
        try {
            $instance = app($strategy, [$models]);
            if ($instance instanceof NamingStrategy) {
                return $instance;
            }
            throw new Exception('Strategy must implement [Ajthinking\Tinx\Naming\NamingStrategy].');
        } catch (Exception $e) {
            switch ($strategy) {
                case 'shortestUnique':
                    return new ShortestUniqueStrategy($models);
                case 'pascal':
                default:
                    return new PascalStrategy($models);
            }
        }
    }
}
