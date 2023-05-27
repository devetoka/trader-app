<?php

namespace App\Exception;

use Exception;
use Throwable;

class MissingUserTokenException extends Exception
{
    public function __construct($message = "token not found", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}