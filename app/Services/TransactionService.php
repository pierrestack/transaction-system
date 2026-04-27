<?php

namespace App\Services;

use App\Models\Transfer;
use App\Resolvers\TransferProcessorResolver;
use App\Services\TransferProcessor\TransactionProcessor;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    private TransactionProcessor $transferProcessor;
    private TransferProcessorResolver $processorResolver;

    public function __construct(TransactionProcessor $transferProcessor, TransferProcessorResolver $transferProcessorResolver)
    {
        $this->transferProcessor = $transferProcessor;
        $this->processorResolver = $transferProcessorResolver;
    }

    public function deposit(array $data)
    {
        return $this->transferProcessor->initProcessDeposit($data);
    }

    public function executeDeposit(string $token)
    {
        return $this->transferProcessor->processDeposit($token);
    }

    public function withdrawal(array $data)
    {
        return $this->transferProcessor->initWithdrawal($data);
    }

    public function executeWithdrawal(string $token)
    {
        return $this->transferProcessor->processWithdrawal($token);
    }

    public function monoTransfer(array $data)
    {
        return $this->transferProcessor->initMonoTransfer($data);
    }

    public function executeTransfer(string $token)
    {
        return DB::transaction(function () use ($token) {

            $transfer = Transfer::where('token', $token)
                ->lockForUpdate()
                ->firstOrFail();

            $processor = $this->processorResolver->resolveMonoTransfer($transfer);

            return $processor->processMonoTransfer($transfer);
        });
    }

    public function multiTransfer(array $data)
    {
        return $this->transferProcessor->initMultiTransfer($data);
    }

    public function executeMultiTransfer(array $tokens)
    {
        return DB::transaction(function () use ($tokens) {

            $transfers = Transfer::whereIn('token', $tokens)
                ->lockForUpdate()
                ->get();

            $processor = $this->processorResolver->resolveMultiTransfer($transfers);

            return $processor->processMultiTransfer($transfers);
        });
    }
}
