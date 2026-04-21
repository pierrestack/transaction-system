<?php

namespace App\Services\Contracts;

use App\Models\Transfer;

interface FeeCalculatorInterface
{
    public function calculateFeeForTransfer(Transfer $transfer): float;
    public function calculateSumFeeForTransfer(array $transfers): float;
    public function calculateSumAmountForTransfer(array $transfers): float;
}
