<?php

namespace App\Repositories\Contracts;

use App\Models\Transfer;
use Illuminate\Support\Collection;

interface TransactionRepositoryInterface
{
    public function initDeposit(string $accountNumber, float $amount, string $description): Transfer;
    public function executeDeposit(Transfer $transfer): Transfer;
    public function initWithdrawal(string $accountNumber, float $amount, string $description): Transfer;
    public function executeWithdrawal(Transfer $transfer): Transfer;
    public function initMonoTransfer(string $fromAccountNumber, string $toAccountNumber, float $amount, string $description): Transfer;
    public function initMultiTransfer(array $transfers, string $description): Collection;
}
