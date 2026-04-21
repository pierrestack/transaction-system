<?php

namespace App\Http\Responses;

use Illuminate\Support\Collection;

class InitMultiTransferResponse extends BaseResponse
{
    /**
     * Create a new class instance.
     */
    public function __construct(Collection $transfers)
    {
        parent::__construct(201, 'Multi-transfer initialized successfully', $transfers->map(function ($transfer) {
            return [
                'token' => $transfer->token,
                'reference' => $transfer->reference,
            ];
        })->toArray());
    }
}
