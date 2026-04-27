<?php

namespace App\Services;

use App\Enums\TypeOperation;
use App\Factories\OperationFactory;
use App\Models\Account;
use App\Models\Operation;
use App\Models\Transfer;

class OperationService
{
    public function credit(Account $account, int $transferId, float $amount): void
    {
        $before = $account->balance;
        $account->increment('balance', $amount);
        $after = $account->balance;

        Operation::create(OperationFactory::make(
            $account->id,
            $transferId,
            TypeOperation::CREDIT->value,
            $amount,
            $before,
            $after
        ));
    }

    public function debit(Account $account, int $transferId, float $amount): void
    {
        $before = $account->balance;
        $account->decrement('balance', $amount);
        $after = $account->balance;

        Operation::create(OperationFactory::make(
            $account->id,
            $transferId,
            TypeOperation::DEBIT->value,
            $amount,
            $before,
            $after
        ));
    }
}
