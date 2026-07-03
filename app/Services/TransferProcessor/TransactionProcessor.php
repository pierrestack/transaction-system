<?php

namespace App\Services\TransferProcessor;

use App\Models\Transfer;
use App\Repositories\Eloquent\TransactionRepository;
use App\Services\Contracts\TransferProcessorInterface;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

abstract class TransactionProcessor implements TransferProcessorInterface
{
    protected TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
    }

    abstract public function supportsMonoTransfers(Transfer $transfer): bool;

    abstract public function supportsMultiTransfers(Collection $transfers): bool;

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

    abstract public function processMonoTransfer(Transfer $transfer): Transfer;

    public function initMultiTransfer(array $data): Collection
    {
        return $this->transactionRepository->initMultiTransfer(
            $data['transfers'],
            $data['description'] ?? ''
        );
    }

    abstract public function processMultiTransfer(Collection $transfers): Collection;
}
