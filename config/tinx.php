<?php

return [

    /**
     * Base paths to search for models (paths ending in '*' search recursively).
     * */
    'model_paths' => [
        '/app',
        '/app/Models/*',
        // '/also/search/this/directory',
        // '/also/search/this/directory/recursively/*',
    ],

    /**
     * Only define these models (all other models will be ignored).
     * */
    'only' => [
        // 'App\OnlyThisModel',
        // 'App\AlsoOnlyThisModel',
    ],

    /**
     * Ignore these models.
     * */
    'except' => [
        // 'App\IgnoreThisModel',
        // 'App\AlsoIgnoreThisModel',
    ],

    /**
     * Model shortcut naming strategy (e.g. 'App\User' = '$u', '$u_', 'u()').
     * Supported values: 'pascal', 'shortestUnique'
     * */
    'strategy' => 'pascal',
    /**
     * Alternatively, you may pass a resolvable fully qualified class name
     * implementing 'Ajthinking\Tinx\Naming\Strategy'.
     * */
    // 'strategy' => App\CustomNamingStrategy::class,

    /**
     * Column name (e.g. 'id', 'created_at') used to determine last model shortcut (i.e. '$u_').
     * */
    'latest_column' => 'created_at',

    /**
     * If true, models without database tables will also have shortcuts defined.
     * */
    'tableless_models' => false,

    /**
     * Include these file(s) before starting tinker.
     * */
    'include' => [
        // '/include/this/file.php',
        // '/also/include/this/file.php',
    ],

    /**
     * Show the console 'Class/Shortcuts' table for up to this many model names, otherwise, hide it.
     * To always view the 'Class/Shortcuts' table regardless of the model name count,
     * pass a 'verbose' flag when booting Tinx (e.g. "php artisan tinx -v"),
     * or set this value to '-1'.
     * */
    'names_table_limit' => 10,
    
];
