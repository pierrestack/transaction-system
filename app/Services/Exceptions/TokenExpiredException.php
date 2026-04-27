<?php

namespace App\Services\Exceptions;

use Exception;

class TokenExpiredException extends Exception
{
    protected $message = 'The transfer token has expired.';
    protected $code = 400;

    public static function make(): self
    {
        return new self();
    }
}
