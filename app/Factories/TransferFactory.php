<?php

namespace App\Factories;

use Illuminate\Support\Str;

class TransferFactory
{
    public static function make(
        string $type,
        ?int $senderId,
        ?int $receiverId,
        float $amount,
        int $currencyId,
        ?string $description = null
    ): array {
        return [
            'token' => (string) Str::uuid(),
            'reference' => strtoupper(substr($type, 0, 3)) . '-' . Str::random(10),
            'type' => $type,
            'sender_account_id' => $senderId,
            'receiver_account_id' => $receiverId,
            'amount' => $amount,
            'status' => 'pending',
            'currency_id' => $currencyId,
            'description' => $description,
            'processed_at' => null,
            'expires_at' => now()->addMinute(10),
        ];
    }
}
