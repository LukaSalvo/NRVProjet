<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);



require_once "vendor/autoload.php";
require_once 'src/classes/dispatch/Dispatcher.php';

use iutnc\nrv\repository\NRVRepository;
use iutnc\nrv\dispatch\Dispatcher;
//echo password_hash("password123", PASSWORD_DEFAULT);
//echo password_hash("adminpassword", PASSWORD_DEFAULT);
session_start();

NRVRepository::setConfig('src/config/db.config.ini');



$dispatcher = new Dispatcher();
$dispatcher->run();

