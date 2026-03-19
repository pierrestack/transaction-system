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
}
