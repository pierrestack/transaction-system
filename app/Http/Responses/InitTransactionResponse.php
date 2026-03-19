<?php

namespace App\Http\Responses;

use App\Models\Transfer;
use Illuminate\Contracts\Support\Responsable;

class InitTransactionResponse implements Responsable
{
    protected Transfer $transfer;

    public function __construct(Transfer $transfer)
    {
        $this->transfer = $transfer;
    }

    public function toResponse($request)
    {
        return response()->json([
            'status' => 'success',
            'message' => $this->transfer->type->getLabel() . ' initialized successfully',
            'data' => [
                'token' => $this->transfer->token,
                'reference' => $this->transfer->reference,
            ]
         ], 201);
    }
}
    