<?php

namespace Ajthinking\Tinx\Naming;

class ForbiddenNames
{
    /**
     * @var array
     * */
    const NAMES = [

        // Reserved keywords.
        '__halt_compiler', 'abstract', 'and', 'array', 'as', 'break', 'callable', 'case',
        'catch', 'class', 'clone', 'const', 'continue', 'declare', 'default', 'die', 'do',
        'echo', 'else', 'elseif', 'empty', 'enddeclare', 'endfor', 'endforeach', 'endif',
        'endswitch', 'endwhile', 'eval', 'exit', 'extends', 'final', 'for', 'foreach',
        'function', 'global', 'goto', 'if', 'implements', 'include', 'include_once',
        'instanceof', 'insteadof', 'interface', 'isset', 'list', 'namespace', 'new', 'or',
        'print', 'private', 'protected', 'public', 'require', 'require_once', 'return',
        'static', 'switch', 'throw', 'trait', 'try', 'unset', 'use', 'var', 'while', 'xor',
    
        // Predefined_constants.
        '__CLASS__', '__DIR__', '__FILE__', '__FUNCTION__', '__LINE__', '__METHOD__', '__NAMESPACE__', '__TRAIT__',
    
        // Used by Tinx.
        're', 'reboot', 'reload', 'restart', 'names', 'tinx_forget_name', 'tinx_query'

    ];

    /**
     * @param string $name
     * @return bool
     * */
    public static function exists($name)
    {
        return in_array($name, static::NAMES);
    }
}
