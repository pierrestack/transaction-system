<?php

namespace App\Services\TransferProcessor;

use App\Models\Transfer;
use App\Repositories\Eloquent\SameTransactionRepository;
use App\Repositories\Eloquent\TransactionRepository;
use App\Services\Contracts\FeeCalculatorInterface;
use App\Services\Contracts\TransferProcessorInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class TransactionProcessor implements TransferProcessorInterface
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = new SameTransactionRepository(app(FeeCalculatorInterface::class));
    }

    public abstract function supportsMonoTransfers(Transfer $transfer): bool;

    public abstract function supportsMultiTransfers(Collection $transfers): bool;

    public function initProcessDeposit(array $data): Transfer
    {
        return $this->transactionRepository->initDeposit(
            $data['account_number'],
            $data['amount'],
            $data['description'] ?? ''
        );
    }

    public function processDeposit(string $token): Transfer
    {
        return DB::transaction(function () use ($token) {

            $transfer = Transfer::where('token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            return $this->transactionRepository->executeDeposit($transfer);
        });
    }

    public function initWithdrawal(array $data): Transfer
    {
        return $this->transactionRepository->initWithdrawal(
            $data['account_number'],
            $data['amount'],
            $data['description'] ?? ''
        );
    }

    public function processWithdrawal(string $token): Transfer
    {
        return DB::transaction(function () use ($token) {

            $transfer = Transfer::where('token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            return $this->transactionRepository->executeWithdrawal($transfer);
        });
    }

    public function initMonoTransfer(array $data): Transfer
    {
        return $this->transactionRepository->initMonoTransfer(
            $data['from_account_number'],
            $data['to_account_number'],
            $data['amount'],
            $data['description'] ?? ''
        );
    }

    public abstract function processMonoTransfer(Transfer $transfer): Transfer;

    public function initMultiTransfer(array $data): Collection
    {
        return $this->transactionRepository->initMultiTransfer(
            $data['transfers'],
            $data['description'] ?? ''
        );
    }

    public abstract function processMultiTransfer(array $transfers): Collection;
}
