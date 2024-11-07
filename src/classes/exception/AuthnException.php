<?php

namespace iutnc\nrv\exception;

use Throwable;

class AuthnException extends \Exception {

    public function __construct($message = "") {
        parent::__construct($message);
    }
}
