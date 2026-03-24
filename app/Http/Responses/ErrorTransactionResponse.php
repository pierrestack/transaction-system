<?php

namespace App\Http\Responses;

class ErrorTransactionResponse extends BaseResponse
{
    public function __construct(string $message)
    {
        parent::__construct(400, $message);
    }
}  