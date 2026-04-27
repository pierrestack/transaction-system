<?php

namespace App\Services\Validator;

use App\Enums\StatusTransfer;
use App\Models\Transfer;
use App\Services\Exceptions\TokenExpiredException;
use App\Services\Exceptions\TransferAlreadyProcessedException;

class TransferValidator
{
    public function validate(Transfer $transfer): void
    {
        if ($transfer->expires_at < now()) {
            throw TokenExpiredException::make();
        }

        if ($transfer->status !== StatusTransfer::PENDING) {
            throw TransferAlreadyProcessedException::make();
        }
    }
}
