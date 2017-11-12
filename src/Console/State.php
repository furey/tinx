<?php

namespace Ajthinking\Tinx\Console;

class State
{
    /**
     * @return bool
     * */
    public static function shouldRestart()
    {
        return static::getStateFileMessage() == "RESTART";
    }

    /**
     * @return void
     * */
    public static function requestRestart()
    {
        static::setStateFileMessage("RESTART");
    }

    /**
     * @return void
     * */
    public static function reset()
    {
        static::setStateFileMessage("TAKE_NO_ACTION");
    }

    /**
     * @param string $message
     * @return string
     * */
    public static function setStateFileMessage($message)
    {
        app('tinx.storage')->put('state', $message);

        return $message;
    }

    /**
     * @return string
     * */
    public static function getStateFileMessage()
    {
        return app('tinx.storage')->get('state');
    }
}
