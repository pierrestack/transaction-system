<?php

namespace App\Repositories\Eloquent;

use App\Models\Account;
use App\Models\Transfer;
use App\Services\Contracts\ConvertionCurrencyInterface;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\OperationService;
use App\Services\Validator\TransferValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class CrossTransactionRepository extends TransactionRepository
{
    private ConvertionCurrencyInterface $convertionCurrency;

    public function __construct(
        FeeCalculatorInterface $feeCalculatorInterface,
        ConvertionCurrencyInterface $convertionCurrency,
        OperationService $operationService,
        TransferValidator $transferValidator
    )
    {
        parent::__construct($feeCalculatorInterface, $operationService, $transferValidator);
        $this->convertionCurrency = $convertionCurrency;
    }

    public function executeMonoTransfer(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {

            $this->transferValidator->validate($transfer);

            $fromAccount = Account::findOrFail($transfer->sender_account_id);

            $toAccount = Account::findOrFail($transfer->receiver_account_id);

            $this->operationService->debit($fromAccount, $transfer->id, $transfer->amount);

            $this->operationService->credit($fromAccount->getSystemAccount(), $transfer->id, $transfer->amount);

            $convertedAmount = $this->convertionCurrency->convertAmount($transfer, $fromAccount, $toAccount);

            $this->operationService->debit($fromAccount->getSystemAccount(), $transfer->id, $transfer->amount);

            $this->operationService->credit($toAccount->getSystemAccount(), $transfer->id, $convertedAmount);

            $this->operationService->debit($toAccount->getSystemAccount(), $transfer->id, $convertedAmount);

            $this->operationService->credit($toAccount, $transfer->id, $convertedAmount);

            $transfer->markCompleted();

            return $transfer;
        });
    }

    public function executeMultiTransfer(Collection $transfers): Collection
    {
        return throw new \Exception('Multi transfer is not implemented yet.');
    }
}
