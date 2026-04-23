<?php

namespace App\Services;

use App\Repositories\Contracts\TransactionRepositoryInterface;

class TransactionService
{
    protected TransactionRepositoryInterface $transactionRepository;

    public function __construct(TransactionRepositoryInterface $transactionRepository)
    {
        $this->transactionRepository = $transactionRepository;
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
        return $this->transactionRepository->initMultiTransfer($transfers, $description);
    }

    public function executeMultiTransfer(array $tokens)
    {
        return $this->transactionRepository->executeMultiTransfer($tokens);
    }
}
