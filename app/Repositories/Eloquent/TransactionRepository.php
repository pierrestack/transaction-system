<?php

namespace App\Repositories\Eloquent;

use App\Factories\OperationFactory;
use App\Factories\TransferFactory;
use App\Models\Account;
use App\Models\Operation;
use App\Models\Transfer;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    public function deposit(string $accountNumber, float $amount, string $description)
    {
        return DB::transaction(function () use ($accountNumber, $amount, $description) {

            $account = Account::where('account_number', $accountNumber)->firstOrFail();

            $before = $account->balance;

            $account->increment('balance', $amount);

            $after = $account->balance;

            $transfer = Transfer::create(TransferFactory::make(
                'deposit',
                null,
                $account->id,
                $amount,
                $account->currency_id,
                $description
            ));

            Operation::create(OperationFactory::make(
                $account->id,
                $transfer->id,
                'credit',
                $amount,
                $before,
                $after
            ));

            return $transfer;
        });
    }
}
