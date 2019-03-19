# Laravel Tinx
[![Latest Stable Version](https://img.shields.io/packagist/v/ajthinking/tinx.svg)](https://packagist.org/packages/ajthinking/tinx)
[![Total Downloads](https://img.shields.io/packagist/dt/ajthinking/tinx.svg)](https://packagist.org/packages/ajthinking/tinx)
[![License](https://img.shields.io/packagist/l/ajthinking/tinx.svg)](https://packagist.org/packages/ajthinking/tinx)

[Laravel Tinker](https://github.com/laravel/tinker), <b>re()</b>loaded.

Reload your session from inside Tinker, plus magic shortcuts for first(), find(), where(), and more!

<img src="https://i.imgur.com/U9NnDix.gif" title="source: imgur.com" />

## Installation

To install Tinx, simply require it via Composer:

```bash
composer require --dev ajthinking/tinx
```

If using Laravel <=5.4, register Tinx's service provider in `config/app.php` (Laravel >=5.5 [does this automatically](https://laravel.com/docs/5.5/packages#package-discovery)):

```php
<?php

// 'config/app.php'

return [
    // etc…
    'providers' => [
        // etc…
        Ajthinking\Tinx\TinxServiceProvider::class,
        // etc…
    ],
    // etc…
];
```

## Usage

From the command line, instead of running `php artisan tinker`, run:

```
php artisan tinx
```

### Reload your Tinker session

To reboot your current session, simply call:

```
re()
```

This will allow you to immediately test out your application's code changes.

Aliases: `reboot()`, `reload()`, `restart()`.

To regenerate Composer's optimized autoload files before rebooting your current session, call:

```
reo()
```

Calling `reo()` simply runs `composer dump -o` before `re()`, ensuring any new classes added to your codebase since starting Tinx are automatically aliasable/resolvable by Laravel Tinker.

### Magic models

Tinx sniffs your models and prepares the following shortcuts:

| Example Shortcut            | Equals                                              |
|:--------------------------- |:--------------------------------------------------- |
| `$u`                        | `App\User::first()`                                 |
| `$u_`                       | `App\User::latest()->first()`                       |
| `$c`                        | `App\Models\Car::first()`                           |
| `u(3)`                      | `App\User::find(3)`                                 |
| `u("gmail")`                | `Where "%gmail%" is found in any column.`           |
| `u("mail", "jon@snow.com")` | `App\User::where("mail", "jon@snow.com")->get()`    |
| `u("id", ">", 0)`           | `App\User::where("id", ">", 0)->get()`              |
| `u()`                       | `"App\User"`                                        |
| `u()::whereRaw(...)`        | `App\User::whereRaw(...) // Note: >= PHP 7.0 only`  |

### Naming strategy

Tinx calculates shortcut names via the implementation defined by your `strategy` config value.

Lets say you have two models: `Car` and `Crocodile`.

If your naming `strategy` was set to **pascal** (default), Tinx would define the following shortcuts in your session:

- Car: `$c`, `$c_`, `c()`
- Crocodile: `$cr`, `$cr_`, `cr()`

### Names

The shortcuts defined for your session will display when Tinx loads and on subsequent reloads.

To see your shortcuts at any time during your session, run:

```
names()
```

Your shortcuts will initially display only if your session satisfies the `names_table_limit` config value.

To filter the shortcuts returned by `names()`, simply pass your filter terms like so:

```
names('car', 'user')
```

## Configuration

Tinx contains a number of helpful configuration options.

To personalise your Tinx installation, publish its config file by running:

```
php artisan vendor:publish --provider=Ajthinking\\Tinx\\TinxServiceProvider --force
```

Once published, edit `config/tinx.php` where appropriate to suit your needs:

```php
<?php

// 'config/tinx.php'

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
```

## Contributing

Please post issues and send PRs.

## License

MIT
