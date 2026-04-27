<?php

namespace App\Repositories\Eloquent;

use App\Enums\TypeAccount;
use App\Enums\TypeFee;
use App\Factories\FreeFactory;
use App\Models\Account;
use App\Models\Fee;
use App\Models\Transfer;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\OperationService;
use App\Services\Validator\TransferValidator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class SameTransactionRepository extends TransactionRepository
{

    public function __construct(FeeCalculatorInterface $feeCalculatorInterface, OperationService $operationService, TransferValidator $transferValidator)
    {
        parent::__construct($feeCalculatorInterface, $operationService, $transferValidator);
    }

    public function executeMonoTransfer(Transfer $transfer): Transfer
    {
        return DB::transaction(function () use ($transfer) {

            $fee = $this->feeCalculatorInterface->calculateFeeForTransfer($transfer);

            $this->transferValidator->validate($transfer);

            $fromAccount = Account::findOrFail($transfer->sender_account_id);

            $toAccount = Account::findOrFail($transfer->receiver_account_id);

            $this->operationService->debit($fromAccount, $transfer->id, $transfer->amount + $fee);

            $this->operationService->credit($toAccount, $transfer->id, $transfer->amount);

            $systemAccount = (new Account(['currency_id' => $transfer->currency_id]))->getAccountSystem();

            Fee::create(FreeFactory::make($transfer->id, TypeFee::FEE_CHARGED->value, $fee));

            $this->operationService->credit($systemAccount, $transfer->id, $fee);

            $transfer->markCompleted();

            return $transfer;
        });
    }

    public function executeMultiTransfer(Collection $transfers): Collection
    {
        return DB::transaction(function () use ($transfers) {

            $fromAccounts = Account::whereIn('id', $transfers->pluck('sender_account_id'))->get()->keyBy('id');
            $toAccounts = Account::whereIn('id', $transfers->pluck('receiver_account_id'))->get()->keyBy('id');
            $systemAccount = Account::where('currency_id', $transfers->first()->currency_id)
                ->where('type', TypeAccount::SYSTEM)
                ->lockForUpdate()
                ->firstOrFail();

            foreach ($transfers as $transfer) {

                $this->transferValidator->validate($transfer);

                $fee = $this->feeCalculatorInterface->calculateFeeForTransfer($transfer);

                $fromAccount = $fromAccounts->get($transfer->sender_account_id);

                $toAccount = $toAccounts->get($transfer->receiver_account_id);

                $this->operationService->debit($fromAccount, $transfer->id, $transfer->amount + $fee);

                $this->operationService->credit($toAccount, $transfer->id, $transfer->amount);

                Fee::create(FreeFactory::make($transfer->id, TypeFee::FEE_CHARGED->value, $fee));

                $this->operationService->credit($systemAccount, $transfer->id, $fee);

                $transfer->markCompleted();
            }

            return $transfers;
        });
    }
}
