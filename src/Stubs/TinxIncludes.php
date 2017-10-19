<?php
use Ajthinking\Tinx\State;

// The function used to restart tinker
function re() {
    State::requestRestart();
    exit();
}

// Insert quick reference array
$TINX_NAMES$

// Return quick reference array
function names()
{    
    return $TINX_NAMES$
}

// Helper to handle all u(x) calls
function getQueryInstance($class, $input)
{
    // INT -> use find
    if(is_int($input)) {
        return $class::find($input);
    }
    // String -> search all columns
    if(is_string($input)) {        
        $columns = ["email", "name"];
        
        $query = $class::select('*');
        
        foreach($columns as $column)
        {
          $query->orWhere($column, '=', $input);
        }
        
        return $query->get();        
    }

    // Catch other stuff
    if($input !== null)
    {
        throw new Exception("Dont know what to do with this datatype. Please make PR.");
    }

    // Return a clean starting point for the query builder
    return $class;
}

// Insert "first" variables, $u etc
$FIRST_MODEL_INSTANCE_VARIABLES$

// Insert model functions
$MODEL_FUNCTIONS$