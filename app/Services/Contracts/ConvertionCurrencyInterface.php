<?php

namespace App\Services\Contracts;

use App\Models\Account;
use App\Models\Transfer;

interface ConvertionCurrencyInterface
{
    public function convertAmount(Transfer $transfer, Account $fromAccount, Account $toAccount): float;
}
