<?php

use \iutnc\nrv\dispatch\Dispatcher;

session_start();


$dispatcher = new Dispatcher();
$dispatcher->run();