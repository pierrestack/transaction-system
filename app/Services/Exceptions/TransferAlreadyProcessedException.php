<?php

namespace App\Services\Exceptions;

use Exception;
class TransferAlreadyProcessedException extends Exception
{
    protected $message = 'This transfer has already been processed.';
    protected $code = 400;

    public static function make(): self
    {
        return new self();
    }
}
