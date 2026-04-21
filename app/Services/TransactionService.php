<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionRepositoryInterface;
use App\Repositories\Eloquent\AdvancedTransactionRepository;

class TransactionService
{
    protected TransactionRepositoryInterface $transactionRepository;
    protected AdvancedTransactionRepository $advancedTransactionRepository;
    
    public function __construct(TransactionRepositoryInterface $transactionRepository, AdvancedTransactionRepository $advancedTransactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
        $this->advancedTransactionRepository = $advancedTransactionRepository;
    }

    public function deposit(string $accountNumber, float $amount, string $description)
    {
        return $this->transactionRepository->initDeposit($accountNumber, $amount, $description);
    }

    public function executeDeposit(string $token)
    {
        return $this->transactionRepository->executeDeposit($token);
    }

    public function withdrawal(string $accountNumber, float $amount, string $description)
    {
        return $this->transactionRepository->initWithdrawal($accountNumber, $amount, $description);
    }

    public function executeWithdrawal(string $token)
    {
        return $this->transactionRepository->executeWithdrawal($token);
    }

    public function transfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description)
    {
        return $this->transactionRepository->initTransfer($fromAccountNumber, $toAccountNumber, $amount, $description);
    }

    public function executeTransfer(string $token)
    {
        return $this->transactionRepository->executeTransfer($token);
    }

    public function multiTransfer(array $transfers, string $description)
    {
        return $this->advancedTransactionRepository->initMultiTransfer($transfers, $description);
    }

    public function executeMultiTransfer(array $tokens)
    {
        return $this->advancedTransactionRepository->executeMultiTransfer($tokens);
    }
}
