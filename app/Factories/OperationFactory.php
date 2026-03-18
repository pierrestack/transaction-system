<?php

namespace App\Factories;

class OperationFactory
{
    public static function make(
        int $accountId,
        int $transferId,
        string $type,
        float $amount,
        float $before,
        float $after
    ): array {
        return [
            'account_id' => $accountId,
            'transfer_id' => $transferId,
            'type' => $type,
            'amount' => $amount,
            'balance_before' => $before,
            'balance_after' => $after,
        ];
    }
}
