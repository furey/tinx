use Ajthinking\Tinx\State;

array_set($GLOBALS, 'tinx.names', {!! var_export($names); !!});

function forgetName($class) {
    array_forget($GLOBALS, "tinx.names.$class");
}

/**
 * The function used to restart tinker.
 * */
function re() {
    State::requestRestart();
    exit();
}

/**
 * Helper to handle all u(x [y, z]) calls.
 * */
function getQueryInstance($class, ...$args)
{
    $totalArgs = count($args);

    /**
     * Zero arguments (i.e. u() returns "App\User").
     * */
    if ($totalArgs === 0) {
        return $class; // Return a clean starting point for the query builder.
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
                throw new Exception("You can only search where there is data. There is no way for Tinx to get a column listing for a model without an existing instance...");
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
$latestColumn = config('tinx.latest_column', 'created_at');
@foreach ($names as $class => $name)
try {
    ${!! $name !!} = {!! $class !!}::first() ?: new {!! $class !!};
    ${!! $name !!}_ = {!! $class !!}::latest($latestColumn)->first() ?: new {!! $class !!};
    if (!function_exists('{!! $name !!}')) {
        function {!! $name !!}(...$args) {
            return getQueryInstance('{!! $class !!}', ...$args);
        }
    }
} catch (\Throwable $e) {
    // Ignoring invalid class files (e.g. those that don't extend Model i.e. 'first()' is undefined, etc).  
} catch (\Exception $e) {
    @if (array_get($config, 'tableless_models'))
    ${!! $name !!} = new {!! $class !!};
    ${!! $name !!}_ = new {!! $class !!};
    if (!function_exists('{!! $name !!}')) {
        function {!! $name !!}(...$args) {
            return '{!! $class !!}';
        }
    }
    @else
    forgetName('{!! $class !!}');
    @endif
}
@endforeach
unset($latestColumn);

/**
 * Return quick reference array.
 * */
function names() {
    return array_get($GLOBALS, 'tinx.names');
}

/**
 * Quick reference array.
 * */
$names = names();
