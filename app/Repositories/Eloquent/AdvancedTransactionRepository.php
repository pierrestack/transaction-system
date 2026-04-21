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
use App\Repositories\Contracts\AdvancedTransactionRepositoryInterface;
use App\Services\Contracts\FeeCalculatorInterface;
use Illuminate\Support\Facades\DB;

class AdvancedTransactionRepository implements AdvancedTransactionRepositoryInterface
{
    private FeeCalculatorInterface $feeCalculatorInterface;

    public function __construct(FeeCalculatorInterface $feeCalculatorInterface)
    {
        $this->feeCalculatorInterface = $feeCalculatorInterface;
    }

    public function initMultiTransfer(array $transfers, string $description)
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

    public function executeMultiTransfer(array $token)
    {
        return DB::transaction(function () use ($token) {

            $transfers = Transfer::whereIn('token', $token)
                ->lockForUpdate()
                ->get();

            $fromAccounts = Account::whereIn('id', $transfers->pluck('sender_account_id'))->get()->keyBy('id');
            $toAccounts = Account::whereIn('id', $transfers->pluck('receiver_account_id'))->get()->keyBy('id');
            $systemAccount = Account::where('currency_id', $transfers->first()->currency_id)
                    ->where('type', TypeAccount::SYSTEM)
                    ->lockForUpdate()
                    ->firstOrFail();

            foreach ($transfers as $transfer) {
                if ($transfer->expires_at < now()) {
                    throw new \Exception('Token expired for transfer with id: ' . $transfer->id);
                }

                if ($transfer->status->value !== StatusTransfer::PENDING->value) {
                    throw new \Exception('Transfer already processed for transfer with id: ' . $transfer->id);
                }

                $fee = $this->feeCalculatorInterface->calculateFeeForTransfer($transfer);

                $fromAccount = $fromAccounts->get($transfer->sender_account_id);
                $toAccount = $toAccounts->get($transfer->receiver_account_id);

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
            }

            return $transfers;
        });
    }
}
