<?php

namespace App\Factories;

class FreeFactory
{
    public static function make(
        int $transferId,
        string $type,
        float $amount = 0.0
    ): array {
        return [
            'transfer_id' => $transferId,
            'type' => $type,
            'amount' => $amount,
        ];
    }
}
