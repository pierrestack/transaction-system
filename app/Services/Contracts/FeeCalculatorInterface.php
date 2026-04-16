<?php

namespace App\Services\Contracts;

use App\Models\Transfer;

interface FeeCalculatorInterface
{
    public function calculate(Transfer $transfer): float;
}
