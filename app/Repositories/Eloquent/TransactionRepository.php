<?php

namespace App\Repositories\Eloquent;

use App\Enums\TypeFee;
use App\Enums\TypeTransfer;
use App\Factories\FreeFactory;
use App\Factories\TransferFactory;
use App\Models\Account;
use App\Models\Fee;
use App\Models\Transfer;
use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\OperationService;
use App\Services\Validator\TransferValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class TransactionRepository implements TransactionRepositoryInterface
{
    protected FeeCalculatorInterface $feeCalculatorInterface;
    protected OperationService $operationService;
    protected TransferValidator $transferValidator;

    public function __construct(FeeCalculatorInterface $feeCalculatorInterface, OperationService $operationService, TransferValidator $transferValidator)
    {
        $this->feeCalculatorInterface = $feeCalculatorInterface;
        $this->operationService = $operationService;
        $this->transferValidator = $transferValidator;
    }

    public function initDeposit(string $accountNumber, float $amount, string $description): Transfer
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

    public function executeDeposit(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {

            $this->transferValidator->validate($transfer);

            $account = Account::findOrFail($transfer->receiver_account_id);

            $this->operationService->credit($account, $transfer->id, $transfer->amount);

            Fee::create(FreeFactory::make(
                $transfer->id,
                TypeFee::FREE_CHARGED->value,
            ));

            $transfer->markCompleted();

            return $transfer;
        });
    }

    public function initWithdrawal(string $accountNumber, float $amount, string $description): Transfer
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

    public function executeWithdrawal(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {

            $this->transferValidator->validate($transfer);

            $account = Account::findOrFail($transfer->sender_account_id);

            $this->operationService->debit($account, $transfer->id, $transfer->amount);

            Fee::create(FreeFactory::make(
                $transfer->id,
                TypeFee::FREE_CHARGED->value,
            ));

            $transfer->markCompleted();

            return $transfer;
        });
    }

    public function initMonoTransfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description): Transfer
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

    public abstract function executeMonoTransfer(Transfer $transfer): Transfer;

    public function initMultiTransfer(array $transfers, string $description): Collection
    {
        return DB::transaction(function () use ($transfers, $description) {

            $sumAmount = $this->feeCalculatorInterface->calculateSumAmountForTransfer($transfers);
            $sumFee = $this->feeCalculatorInterface->calculateSumFeeForTransfer($transfers);

            $fromAccount = Account::where('account_number', $transfers[0]['from_account_number'])->firstOrFail();
            $toAccountNumbers = array_column($transfers, 'to_account_number');
            $toAccounts = Account::whereIn('account_number', $toAccountNumbers)->get()->keyBy('account_number');

            if ($fromAccount->balance < $sumAmount + $sumFee) {
                throw new \Exception('Insufficient balance for multi-transfer');
            }

            foreach ($transfers as $transfer) {
                $transferFactory = TransferFactory::make(
                    TypeTransfer::TRANSFER->value,
                    $fromAccount->id,
                    $toAccounts->get($transfer['to_account_number'])->id,
                    $transfer['amount'],
                    $fromAccount->currency_id,
                    $description
                );

                $transfersList[] = Transfer::create($transferFactory);
            }

            return collect($transfersList);
        });
    }

    public abstract function executeMultiTransfer(Collection $transfers): Collection;
}
