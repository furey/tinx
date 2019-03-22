@if ((bool) array_get($config, 'tableless_models') === true)
    try {
        ${!! $name !!} = app('{!! $class !!}');
        ${!! $name !!}_ = app('{!! $class !!}');
        array_set($GLOBALS, 'tinx.shortcuts.{!! $name !!}', ${!! $name !!});
        array_set($GLOBALS, 'tinx.shortcuts.{!! $name !!}_', ${!! $name !!}_);
        if (!function_exists('{!! $name !!}')) {
            function {!! $name !!}(...$args) {
                return '{!! $class !!}';
            }
        }
    } catch (Throwable $e) {
        tinx_forget_name('{!! $class !!}');
    } catch (Exception $e) {
        tinx_forget_name('{!! $class !!}');
    }
@else
    tinx_forget_name('{!! $class !!}');
@endif
