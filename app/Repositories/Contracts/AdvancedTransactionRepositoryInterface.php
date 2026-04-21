<?php

namespace App\Repositories\Contracts;

interface AdvancedTransactionRepositoryInterface
{
    public function initMultiTransfer(array $transfers, string $description);
    public function executeMultiTransfer(array $token);
}
