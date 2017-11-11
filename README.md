# Laravel Tinx
[![Latest Stable Version](https://poser.pugx.org/ajthinking/tinx/v/stable)](https://packagist.org/packages/ajthinking/tinx)
[![Total Downloads](https://poser.pugx.org/ajthinking/tinx/downloads)](https://packagist.org/packages/ajthinking/tinx)
[![License](https://poser.pugx.org/ajthinking/tinx/license)](https://packagist.org/packages/ajthinking/tinx)

[Laravel Tinker](https://github.com/laravel/tinker), <b>re()</b>loaded.

Reload your session from inside Tinker plus automatic super shortcuts for first(), find(), search, and more!

<img src="https://i.imgur.com/MjTd9kG.gif" title="source: imgur.com" />

## Installation

```bash
composer require ajthinking/tinx
```

That's it. This package supports Laravel [Package Discovery](https://laravel.com/docs/5.5/packages#package-discovery).

## Usage

```php
php artisan tinx
```

### Reload your Tinker session

```bash
re()
```

This will allow you to immediately test out your application's code changes.

Aliases:

- `reboot()`
- `reload()`
- `restart()`

### Magic models

Tinx sniffs your models and prepares the following shortcuts.

| Example Shortcut            | Equals                                           |
|:--------------------------- |:------------------------------------------------ |
| `$u`                        | `App\User::first()`                              |
| `$u_`                       | `App\User::latest()->first()`                    |
| `$c`                        | `App\Models\Car::first()`                        |
| `u(3)`                      | `App\User::find(3)`                              |
| `u("gmail")`                | `Where "%gmail%" is found in any column.`        |
| `u("mail", "jon@snow.com")` | `App\User::where("mail", "jon@snow.com")->get()` |
| `u("id", ">", 0)`           | `App\User::where("id", ">", 0)->get()`           |
| `u()`                       | `"App\User"`                                     |
| `u()::whereRaw(...)`        | `App\User::whereRaw(...) // chain as needed`     |

### Naming strategy

Tinx calculates its shortcut names via your `strategy` config value implementation.

Lets say you have two models: `Car` and `Crocodile`.

If your naming `strategy` was set to **pascal** (default), Tinx would define the following shortcuts into your session:

- Car: `$c`, `$c_`, `c()`
- Crocodile: `$cr`, `$cr_`, `cr()`

### Names

The shortcuts defined for your session will display when Tinx loads and on subsequent reloads.

To see your shortcuts from any time within your session, run:

```bash
names()
```

Your shortcuts will only initially display if your session satisfies your `names_table_limit` config value.

## Configuration


#### Configuration

Tinx contains a number helpful configuration options so you can tweak it to suit your needs.

To publish Tinx's config file into your application, run:

```php
php artisan vendor:publish --provider=Ajthinking\\Tinx\\TinxServiceProvider --force
```

```php
<?php

// 'config/tinx.php'

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
     * Supported: 'pascal', 'shortestUnique', or any class implementing 'Ajthinking\Tinx\Naming\Strategy'.
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
```

## Contributing

Please post issues and send PRs.

### Suggested improvments

* Add more tests (currently only naming tests are implemented).
* Eloquent should support static calls to `getColumnListing`. Workaround? 

## License

MIT
