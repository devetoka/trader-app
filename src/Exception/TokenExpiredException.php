<?php

namespace App\Exception;

use Exception;
use Throwable;

class TokenExpiredException extends Exception
{
    public function __construct($message = "Token expired", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

}