<?php

namespace App\Http\Responses;

use Illuminate\Contracts\Support\Responsable;

class ErrorTransactionResponse implements Responsable
{
    protected string $message;

    public function __construct(string $message)
    {
        $this->message = $message;
    }

    public function toResponse($request)
    {
        return response()->json([
            'status' => 'error',
            'message' => $this->message
        ], 400);
    }
}  