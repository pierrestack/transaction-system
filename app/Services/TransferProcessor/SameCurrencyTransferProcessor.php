<?php

namespace App\Services\TransferProcessor;

use App\Models\Transfer;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Support\Collection;

class SameCurrencyTransferProcessor extends TransactionProcessor
{
    public function __construct(TransactionRepository $transactionRepository)
    {
        parent::__construct($transactionRepository);
    }

    public function supportsMonoTransfers(Transfer $transfer): bool
    {
        return $transfer->senderAccount->currency_id === $transfer->receiverAccount->currency_id;
    }

    public function supportsMultiTransfers(Collection $transfers): bool
    {
        return true;
    }

    public function processMonoTransfer(Transfer $transfer): Transfer
    {
        return $this->transactionRepository->executeMonoTransfer($transfer);
    }

    public function processMultiTransfer(array $transfers): Collection
    {
        return $this->transactionRepository->executeMultiTransfer($transfers);
    }
}
