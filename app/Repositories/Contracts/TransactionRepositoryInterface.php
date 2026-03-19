<?php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    public function initDeposit(string $accountNumber, float $amount, string $description);
    public function executeDeposit(string $token);
    public function initWithdrawal(string $accountNumber, float $amount, string $description);
    public function executeWithdrawal(string $token);
    public function initTransfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description);
    public function executeTransfer(string $token);
}
