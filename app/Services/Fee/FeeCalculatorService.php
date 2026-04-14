<?php

namespace App\Services\Fee;

use App\Models\Transfer;
use App\Services\Contracts\FeeCalculatorInterface;

class FeeCalculatorService implements FeeCalculatorInterface
{
    public function calculate(Transfer $transfer): float
    {
        return $transfer->amount * 0.1;
    }
}
