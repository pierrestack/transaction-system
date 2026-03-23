<?php

namespace App\Http\Responses;

use App\Models\Transfer;
use Illuminate\Contracts\Support\Responsable;

class ExecuteTransactionResponse implements Responsable
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
            'message' => $this->transfer->type->getLabel() . ' executed successfully',
            'data' => $this->transfer
         ], 201);
    }
}