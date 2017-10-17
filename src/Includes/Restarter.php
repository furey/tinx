<?php

use Ajthinking\Tinx\State;

    function re() {
        State::requestRestart();
        exit();
    }

    $u = \App\User::first();
    $v = "someOtherValue";
    $w = 4;