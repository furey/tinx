<php

use Ajthinking\Tinx\Console\State;
use Illuminate\Support\Arr;

/**
 * Restarts Tinker.
 *
 * @return void
 * */
function re() {
    State::requestRestart();
    exit();
}

/**
 * Regenerate Composer's optimized autoload files before restarting Tinker.
 *
 * @return void
 * */
function reo() {
    exec("composer dump -o");
    re();
}

/**
 * Aliases.
 * */
function reboot() {
    re();
}

function reload() {
    re();
}

function restart() {
    re();
}

/**
 * Renders the "Class/Shortcuts" names table.
 *
 * @param array $args If passed, filters classes to these terms (e.g. "names('banana', 'carrot')").
 * @return void
 * */
function names(...$args) {
    event('tinx.names', $args);
}

/**
 * @param string $class
 * @return void
 * */
function tinx_forget_name($class) {
    Arr::forget($GLOBALS, "tinx.names.$class");
}

/**
 * Magic query method to handle all "u(x [y, z])" calls.
 *
 * @param string $class
 * @param mixed $args
 * @return mixed
 * */
function tinx_query($class, ...$args)
{
    $totalArgs = count($args);

    /**
     * Zero arguments (i.e. u() returns "App\User").
     * */
    if ($totalArgs === 0) {
        return $class; /* Return a clean starting point for the query builder. */
    }

    /**
     * One argument (i.e. u(2) returns App\User::find(2)).
     * */
    if ($totalArgs === 1) {
        $arg = $args[0];

        /**
         * Int? Use "find()".
         * */
        if (is_int($arg)) {
            return $class::find($arg);
        }

        /**
         * String? Search all columns.
         * */
        if (is_string($arg)) {
            if ($class::first() === null) {
                throw new Exception(
                    "You can only search where there is data. ".
                    "There is no way for Tinx to get a column listing ".
                    "for a model without an existing instance…");
            }
            $columns = Schema::getColumnListing($class::first()->getTable());
            $query = $class::select('*');
            foreach ($columns as $column) {
                $query->orWhere($column, 'like', '%'.$arg.'%');
            }
            return $query->get();
        }

        throw new Exception("Don't know what to do with this datatype. Please make PR.");
    }

    /**
     * The query builder's "where" method accepts up to 4 arguments, but let's lock it to 3.
     * Two arguments (i.e. u("name", "Anders") returns App\User::where("name", "Anders")).
     * Three arguments (i.e. u("id", ">", 1) returns App\User::where("id", ">", 1)).
     * */
    if ($totalArgs >= 2 && $totalArgs <= 3) {
        return $class::where(...$args)->get();
    }
    
    throw new Exception("Too many arguments!");
}

/**
 * Insert "first" and "last" variables (e.g. '$u', '$u_', etc) and model functions (e.g. 'u()', etc).
 * For "first" variable, returns "::first()" if class DB table exists, otherwise "new" (if 'tableless_models' set to true).
 * For "last" variable, returns "::latest()->first()" if class DB table exists, otherwise "new" (if 'tableless_models' set to true).
 * */
Arr::set($GLOBALS, 'tinx.names', {!! var_export($names); !!});
$latestColumn = '{{ Arr::get($config, 'latest_column', 'created_at') }}';
@foreach ($names as $class => $name)
    try {
        ${!! $name !!} = {!! $class !!}::first() ?: app('{!! $class !!}');
        ${!! $name !!}_ = {!! $class !!}::latest($latestColumn)->first() ?: app('{!! $class !!}');
        Arr::set($GLOBALS, 'tinx.shortcuts.{!! $name !!}', ${!! $name !!});
        Arr::set($GLOBALS, 'tinx.shortcuts.{!! $name !!}_', ${!! $name !!}_);
        if (!function_exists('{!! $name !!}')) {
            function {!! $name !!}(...$args) {
                return tinx_query('{!! $class !!}', ...$args);
            }
        }
    } catch (Throwable $e) {
        @include('tinx::on-name-error')
    } catch (Exception $e) {
        @include('tinx::on-name-error')
    }
@endforeach
unset($latestColumn);

/**
 * Quick names reference array.
 * */
$names = Arr::get($GLOBALS, 'tinx.names');

/**
 * Define shortcuts for "names()" table, and also set quick shortcuts reference array.
 * */
$shortcuts = collect($names)->map(function ($name, $class) {
    $shortcuts = [];
    if (Arr::has($GLOBALS, "tinx.shortcuts.$name")) $shortcuts[] = "\${$name}";
    if (Arr::has($GLOBALS, "tinx.shortcuts.{$name}_")) $shortcuts[] = "\${$name}_";
    if (function_exists($name)) $shortcuts[] = "{$name}()";
    return implode(', ', $shortcuts);
})->all();
Arr::set($GLOBALS, 'tinx.names', $shortcuts);

/**
 * Conditionally render the "Class/Shortcuts" names table.
 * */
event('tinx.names.conditional');
