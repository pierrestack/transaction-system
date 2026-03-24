<?php

namespace App\Http\Responses;

use App\Models\Transfer;

class ExecuteTransactionResponse extends BaseResponse
{
    public function __construct(Transfer $transfer)
    {
        parent::__construct(201, $transfer->type->getLabel() . ' executed successfully', $transfer);
    }
}