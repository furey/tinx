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
    // Int -> use find
    if(is_int($input)) {
        return $class::find($input);
    }
    // String -> search all columns
    if(is_string($input)) {
        if($class::first() == null)
        {
            throw new Exception("You can only search where there is data. There is no way for Tinx to get a column listing for a model without an existing instance...");
        }

        $columns = Schema::getColumnListing($class::first()->getTable());

        $query = $class::select('*');

        foreach($columns as $column)
        {
          $query->orWhere($column, 'like', '%' . $input . '%');
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

// Insert "last" variables, $u_ etc
$LAST_MODEL_INSTANCE_VARIABLES$

// Insert model functions
$MODEL_FUNCTIONS$
