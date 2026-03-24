<?php

namespace App\Http\Responses;

use App\Models\Transfer;

class InitTransactionResponse extends BaseResponse
{
    public function __construct(Transfer $transfer)
    {
        parent::__construct(201, $transfer->type->getLabel() . ' initialized successfully', [
            'token' => $transfer->token,
            'reference' => $transfer->reference,
        ]);
    }
}
