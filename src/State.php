<?php

namespace Ajthinking\Tinx;

class State
{
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
        resolve('tinx.storage')->put('state', $message);
        return $message;
    }

    public static function getStateFileMessage()
    {
        return resolve('tinx.storage')->get('state');
    }
}
