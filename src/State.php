<?php

namespace Ajthinking\Tinx;

class State
{
    public function __construct() 
    {

    }

    public static function shouldRestart()
    {
        return State::getStateFileMessage() == "RESTART";
    }

    public static function requestRestart()
    {
        State::setStateFileMessage("RESTART");
    }
    
    public static function reset()
    {
        State::setStateFileMessage("TAKE_NO_ACTION");
    }
    
    public static function setStateFileMessage($message)
    {
        $file = fopen("storage/tinx.state", "w") or die("Unable to open tinx state file!");
        fwrite($file, $message);
        fclose($file);
        return $message;
    }

    public static function getStateFileMessage()
    {
        $file = fopen("storage/tinx.state", "r") or die("Unable to open tinx state file!");
        $message = fgets($file);
        fclose($file);        
        return $message;
    }    
}


