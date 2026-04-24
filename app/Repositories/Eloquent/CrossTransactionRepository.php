<?php

namespace App\Repositories\Eloquent;

use App\Models\Transfer;
use App\Services\Contracts\FeeCalculatorInterface;
use Illuminate\Support\Collection;

class CrossTransactionRepository extends TransactionRepository
{
    public function __construct(FeeCalculatorInterface $feeCalculatorInterface)
    {
        parent::__construct($feeCalculatorInterface);
    }

    public function executeMonoTransfer(Transfer $transfer): Transfer
    {
        // TODO: Implement executeTransfer() method.
    }

    public function executeMultiTransfer(Collection $transfers): Collection
    {
        // TODO: Implement executeMultiTransfer() method.
    }
}
