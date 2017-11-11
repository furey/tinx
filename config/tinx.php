<?php

return [

    /**
     * The namespaces and relating base paths to search for models.
     * */
    'namespaces_and_paths' => [
        'App' => '/app',
        'App\Models' => '/app/Models',
        // 'Another\Namespace' => '/path/to/another/namespace/models'
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
     * Model shortcut naming strategy (e.g. 'App\User' = '$u', 'u()').
     * Supported: 'pascal', 'shortestUnique'
     * Also supports any resolvable full class name implementing 'Ajthinking\Tinx\Naming\Strategy'.
     * */
    'strategy' => 'pascal',

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
