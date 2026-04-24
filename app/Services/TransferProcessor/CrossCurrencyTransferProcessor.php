<?php

namespace App\Services\TransferProcessor;

use App\Models\Transfer;
use App\Repositories\Eloquent\TransactionRepository;
use Illuminate\Support\Collection;

class CrossCurrencyTransferProcessor extends TransactionProcessor
{
    private TransactionRepository $transactionRepository;

    public function __construct(TransactionRepository $transactionRepository)
    {
        parent::__construct($transactionRepository);
        $this->transactionRepository = $transactionRepository;
    }

    public function supportsMonoTransfers(Transfer $transfer): bool
    {
        return $transfer->senderAccount->currency_id !== $transfer->receiverAccount->currency_id;
    }

    public function supportsMultiTransfers(Collection $transfers): bool
    {
        return false;
    }

    public function processMonoTransfer(Transfer $transfer): Transfer
    {
        // TODO: Implement processMonoTransfer() method.
    }


    public function processMultiTransfer(array $transfers): Collection
    {
        // TODO: Implement processMultiTransfer() method.
    }
}
