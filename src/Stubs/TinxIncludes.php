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

// Helper to handle all u(x [y, z]) calls
// Default values for parameters are "TINX_IGNORE_VALUE to allow for null searching
function getQueryInstance($class, $input1, $input2, $input3)
{
    // zero paramaters, ie u() returns "App\User"
    if($input1 == "TINX_IGNORE_VALUE" && $input2 == "TINX_IGNORE_VALUE" && $input3 == "TINX_IGNORE_VALUE")
    {        
        // Return a clean starting point for the query builder
        return $class;
    }

    // one paramater, ie u(2) returns App\User::find(2)
    if($input1 != "TINX_IGNORE_VALUE" && $input2 == "TINX_IGNORE_VALUE" && $input3 == "TINX_IGNORE_VALUE")
    {        
        // Int -> use find
        if(is_int($input1)) {
            return $class::find($input1);
        }
        // String -> search all columns
        if(is_string($input1)) {
            if($class::first() == null)
            {
                throw new Exception("You can only search where there is data. There is no way for Tinx to get a column listing for a model without an existing instance...");
            }

            $columns = Schema::getColumnListing($class::first()->getTable());

            $query = $class::select('*');

            foreach($columns as $column)
            {
            $query->orWhere($column, 'like', '%' . $input1 . '%');
            }

            return $query->get();
        }

        // Catch other stuff
        throw new Exception("Dont know what to do with this datatype. Please make PR.");
    }

    // two paramaters, ie u("name", "Anders") returns App\User::where("name", "Anders")
    if($input1 != "TINX_IGNORE_VALUE" && $input2 != "TINX_IGNORE_VALUE" && $input3 == "TINX_IGNORE_VALUE")
    {
        return $class::where($input1, $input2)->get();
    }
    
    // three paramaters, ie u("id", ">", 1) returns App\User::where("id", ">", 1)
    if($input1 != "TINX_IGNORE_VALUE" && $input2 != "TINX_IGNORE_VALUE" && $input3 != "TINX_IGNORE_VALUE")
    {
        return $class::where($input1, $input2, $input3)->get();
    }    
}

// Insert "first" variables, $u etc
$FIRST_MODEL_INSTANCE_VARIABLES$

// Insert "last" variables, $u_ etc
$LAST_MODEL_INSTANCE_VARIABLES$

// Insert model functions
$MODEL_FUNCTIONS$
