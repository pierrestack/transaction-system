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
    public function initDeposit(string $accountNumber, float $amount, string $description)
    {
        return DB::transaction(function () use ($accountNumber, $amount, $description) {

            $account = Account::where('account_number', $accountNumber)->firstOrFail();

            $transfer = Transfer::create(TransferFactory::make(
                'deposit',
                null,
                $account->id,
                $amount,
                $account->currency_id,
                $description
            ));

            return $transfer;
        });
    }

    public function executeDeposit(string $token)
    {
        return DB::transaction(function () use ($token) {

            $transfer = Transfer::where('token', $token)
                ->lockForUpdate()
                ->firstOrFail();
            
            if ($transfer->expires_at < now()) {
                throw new \Exception('Token expired');
            }

            if ($transfer->status->value !== 'pending') {
                throw new \Exception('Transfer already processed');
            }

            $account = Account::findOrFail($transfer->receiver_account_id);

            $before = $account->balance;

            $account->increment('balance', $transfer->amount);

            $after = $account->balance;

            Operation::create(OperationFactory::make(
                $account->id,
                $transfer->id,
                'credit',
                $transfer->amount,
                $before,
                $after
            ));

            $transfer->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            return $transfer;
        });
    }
}
