<?php

namespace App\Services\Fee;

use App\Models\Transfer;
use App\Services\Contracts\FeeCalculatorInterface;

class FeeCalculatorService implements FeeCalculatorInterface
{
    public function calculateFeeForTransfer(Transfer $transfer): float
    {
        return $transfer->amount * 0.1;
    }

    public function calculateSumFeeForTransfer(array $transfers): float
    {
        $totalFee = 0;

        foreach ($transfers as $transfer) {
            $totalFee += $this->calculateFeeForTransfer(new Transfer($transfer));
        }

        return $totalFee;
    }

    public function calculateSumAmountForTransfer(array $transfers): float
    {
        $totalAmount = 0;

        foreach ($transfers as $transfer) {
            $totalAmount += $transfer['amount'];
        }

        return $totalAmount;
    }
}
