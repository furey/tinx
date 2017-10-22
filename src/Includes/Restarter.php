<?php

use Ajthinking\Tinx\State;

function re() {
    State::requestRestart();
    exit();
}
