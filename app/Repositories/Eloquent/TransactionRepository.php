<?php

namespace App\Repositories\Eloquent;

use App\Enums\TypeFee;
use App\Factories\FreeFactory;
use App\Factories\OperationFactory;
use App\Factories\TransferFactory;
use App\Models\Account;
use App\Models\Fee;
use App\Models\Operation;
use App\Models\Transfer;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Contracts\FeeCalculatorInterface;
use Illuminate\Support\Facades\DB;

class TransactionRepository implements TransactionRepositoryInterface
{
    private FeeCalculatorInterface $feeCalculatorInterface;

    public function __construct(FeeCalculatorInterface $feeCalculatorInterface)
    {
        $this->feeCalculatorInterface = $feeCalculatorInterface;
    }

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

            Fee::create(FreeFactory::make(
                $transfer->id,
                strtolower(TypeFee::FREE_CHARGED->getLabel()),
            ));

            return $transfer;
        });
    }
    
    public function initWithdrawal(string $accountNumber, float $amount, string $description)
    {
        return DB::transaction(function () use ($accountNumber, $amount, $description) {

            $account = Account::where('account_number', $accountNumber)->firstOrFail();

            if ($account->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $transfer = Transfer::create(TransferFactory::make(
                'withdrawal',
                $account->id,
                null,
                $amount,
                $account->currency_id,
                $description
            ));

            return $transfer;
        });
    }

    public function executeWithdrawal(string $token)
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

            $account = Account::findOrFail($transfer->sender_account_id);

            $before = $account->balance;

            $account->decrement('balance', $transfer->amount);

            $after = $account->balance;

            Operation::create(OperationFactory::make(
                $account->id,
                $transfer->id,
                'debit',
                $transfer->amount,
                $before,
                $after
            ));

            $transfer->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            Fee::create(FreeFactory::make(
                $transfer->id,
                strtolower(TypeFee::FREE_CHARGED->getLabel()),
            ));

            return $transfer;
        });
    }

    public function initTransfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description)
    {
        return DB::transaction(function () use ($fromAccountNumber, $toAccountNumber, $amount, $description) {

            $fromAccount = Account::where('account_number', $fromAccountNumber)->firstOrFail();
            $toAccount = Account::where('account_number', $toAccountNumber)->firstOrFail();

            if ($fromAccount->balance < $amount) {
                throw new \Exception('Insufficient balance');
            }

            $transfer = Transfer::create(TransferFactory::make(
                'transfer',
                $fromAccount->id,
                $toAccount->id,
                $amount,
                $fromAccount->currency_id,
                $description
            ));

            return $transfer;
        });
    }

    public function executeTransfer(string $token)
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

            $fromAccount = Account::findOrFail($transfer->sender_account_id);
            $toAccount = Account::findOrFail($transfer->receiver_account_id);

            if ($fromAccount->balance < $transfer->amount) {
                throw new \Exception('Insufficient balance');
            }

            // Debit from account
            $beforeFrom = $fromAccount->balance;
            $fromAccount->decrement('balance', $transfer->amount);
            $afterFrom = $fromAccount->balance;

            Operation::create(OperationFactory::make(
                $fromAccount->id,
                $transfer->id,
                'debit',
                $transfer->amount,
                $beforeFrom,
                $afterFrom
            ));

            // Credit to account
            $beforeTo = $toAccount->balance;
            $toAccount->increment('balance', $transfer->amount);
            $afterTo = $toAccount->balance;

            Operation::create(OperationFactory::make(
                $toAccount->id,
                $transfer->id,
                'credit',
                $transfer->amount,
                $beforeTo,
                $afterTo
            ));

            // Update transfer status
            $transfer->update([
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            Fee::create(FreeFactory::make(
                $transfer->id,
                strtolower(TypeFee::FEE_CHARGED->getLabel()),
                $this->feeCalculatorInterface->calculate($transfer),
            ));

            return $transfer;
        });
    }
}
