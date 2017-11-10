<?php

return [

    /**
     * The namespaces and relating base paths to search for models.
     * */
    'namespaces_and_paths' => [
        'App' => '/app',
        'App\Models' => '/app/Models',
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
     * Model variable/function naming strategy (e.g. 'User' ---> '$u'/'u()').
     * Supported: 'pascal', 'shortestUnique', or any class implementing 'Ajthinking\Tinx\Naming\NamingStrategy'.
     * */
    'strategy' => 'pascal',

    /**
     * Last model variable (i.e. '$u_') "latest()" column name.
     * */
    'latest_column' => 'created_at',

    /**
     * If true, models without database tables will also be defined.
     * */
    'tableless_models' => false,

    /**
     * Include these file(s) before starting tinker.
     * */
    'include' => [
        // include/this/file.php,
        // also/include/this/file.php,
    ],

    /**
     * Show the console 'Class/Shortcuts' table for up to this many model names, otherwise, hide it.
     * To always view the 'Class/Shortcuts' table regardless of the model name count,
     * pass a 'verbose' flag on boot (e.g. "php artisan tinx -v"), or set this value to '-1'.
     * */
    'names_table_limit' => 10,
    
];
