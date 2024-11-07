<?php



namespace iutnc\nrv\exception;

class InvalidPropertyNameException extends \Exception{

    public function __construct($message = ""){
        parent::__construct($message);
    }
}