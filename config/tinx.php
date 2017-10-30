<?php

return [

    /**
     * The namespaces relating base paths to search for models.
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
     * */
    'strategy' => 'shortestUnique',

    /**
     * Last model variable (i.e. '$u_') "latest()" (i.e. order by) column name.
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
    
];
