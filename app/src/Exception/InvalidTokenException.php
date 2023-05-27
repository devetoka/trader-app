<?php

namespace App\Exception;

use Exception;
use Throwable;

class InvalidTokenException extends Exception
{
    public function __construct($message = "token is invalid", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}