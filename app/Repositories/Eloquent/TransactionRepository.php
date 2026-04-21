<?php

namespace App\Repositories\Eloquent;

use App\Enums\StatusTransfer;
use App\Enums\TypeAccount;
use App\Enums\TypeFee;
use App\Enums\TypeOperation;
use App\Enums\TypeTransfer;
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
                TypeTransfer::DEPOSIT->value,
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

            if ($transfer->status->value !== StatusTransfer::PENDING->value) {
                throw new \Exception('Transfer already processed');
            }

            $account = Account::findOrFail($transfer->receiver_account_id);

            $before = $account->balance;

            $account->increment('balance', $transfer->amount);

            $after = $account->balance;

            Operation::create(OperationFactory::make(
                $account->id,
                $transfer->id,
                TypeOperation::CREDIT->value,
                $transfer->amount,
                $before,
                $after
            ));

            $transfer->update([
                'status' => StatusTransfer::COMPLETED->value,
                'processed_at' => now(),
            ]);

            Fee::create(FreeFactory::make(
                $transfer->id,
                TypeFee::FREE_CHARGED->value,
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
                TypeTransfer::WITHDRAWAL->value,
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

            if ($transfer->status->value !== StatusTransfer::PENDING->value) {
                throw new \Exception('Transfer already processed');
            }

            $account = Account::findOrFail($transfer->sender_account_id);

            $before = $account->balance;

            $account->decrement('balance', $transfer->amount);

            $after = $account->balance;

            Operation::create(OperationFactory::make(
                $account->id,
                $transfer->id,
                TypeOperation::DEBIT->value,
                $transfer->amount,
                $before,
                $after
            ));

            $transfer->update([
                'status' => StatusTransfer::COMPLETED->value,
                'processed_at' => now(),
            ]);

            Fee::create(FreeFactory::make(
                $transfer->id,
                TypeFee::FREE_CHARGED->value,
            ));

            return $transfer;
        });
    }

    public function initTransfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description)
    {
        return DB::transaction(function () use ($fromAccountNumber, $toAccountNumber, $amount, $description) {

            $fromAccount = Account::where('account_number', $fromAccountNumber)->firstOrFail();
            $toAccount = Account::where('account_number', $toAccountNumber)->firstOrFail();

            $transferFactory = TransferFactory::make(
                TypeTransfer::TRANSFER->value,
                $fromAccount->id,
                $toAccount->id,
                $amount,
                $fromAccount->currency_id,
                $description
            );

            $fee = $this->feeCalculatorInterface->calculateFeeForTransfer(new Transfer($transferFactory));

            if ($fromAccount->balance < $amount + $fee) {
                throw new \Exception('Insufficient balance');
            }

            $transfer = Transfer::create($transferFactory);

            return $transfer;
        });
    }

    public function executeTransfer(string $token)
    {
        return DB::transaction(function () use ($token) {

            $transfer = Transfer::where('token', $token)
                ->lockForUpdate()
                ->firstOrFail();
            
            $fee = $this->feeCalculatorInterface->calculateFeeForTransfer($transfer);
            
            if ($transfer->expires_at < now()) {
                throw new \Exception('Token expired');
            }

            if ($transfer->status->value !== StatusTransfer::PENDING->value) {
                throw new \Exception('Transfer already processed');
            }

            $fromAccount = Account::findOrFail($transfer->sender_account_id);
            $toAccount = Account::findOrFail($transfer->receiver_account_id);

            // Debit from account
            $beforeFrom = $fromAccount->balance;
            $fromAccount->decrement('balance', $transfer->amount + $fee);
            $afterFrom = $fromAccount->balance;

            Operation::create(OperationFactory::make(
                $fromAccount->id,
                $transfer->id,
                TypeOperation::DEBIT->value,
                $transfer->amount + $fee,
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
                TypeOperation::CREDIT->value,
                $transfer->amount,
                $beforeTo,
                $afterTo
            ));

            // Update transfer status
            $transfer->update([
                'status' => StatusTransfer::COMPLETED->value,
                'processed_at' => now(),
            ]);

            $systemAccount = Account::where('currency_id', $transfer->currency_id)
                ->where('type', TypeAccount::SYSTEM)
                ->lockForUpdate()
                ->firstOrFail();

            $beforeSystem = $systemAccount->balance;
            $systemAccount->increment('balance', $fee);
            $afterSystem = $systemAccount->balance;

            Fee::create(FreeFactory::make(
                $transfer->id,
                TypeFee::FEE_CHARGED->value,
                $fee
            ));

            Operation::create(OperationFactory::make(
                $systemAccount->id,
                $transfer->id,
                TypeOperation::CREDIT->value,
                $fee,
                $beforeSystem,
                $afterSystem
            ));

            return $transfer;
        });
    }
}
