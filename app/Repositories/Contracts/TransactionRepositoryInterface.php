<?php

namespace App\Repositories\Contracts;

interface TransactionRepositoryInterface
{
    public function deposit(string $accountNumber, float $amount, string $description);
}
