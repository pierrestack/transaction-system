<?php

namespace App\Services\Currency;

use App\Events\TransferConverted;
use App\Models\Account;
use App\Models\ExchangeRate;
use App\Models\Transfer;
use App\Services\Contracts\ConvertionCurrencyInterface;

class ConvertCurrencyService implements ConvertionCurrencyInterface
{
    private ExchangeRate $exchangeRate;

    public function __construct(ExchangeRate $exchangeRate)
    {
        $this->exchangeRate = $exchangeRate;
    }

    public function convertAmount(Transfer $transfer, Account $fromAccount, Account $toAccount): float
    {
        $exchangeRateFromAndToCurrency = $this->exchangeRate->getExchangeRateFromAndToCurrency(
            $fromAccount->currency_id,
            $toAccount->currency_id
        );

        if (!$exchangeRateFromAndToCurrency) {
            throw new \Exception('Exchange rate not found for the given currencies.');
        }

        $convertAmount = $transfer->amount * $exchangeRateFromAndToCurrency->rate;

        TransferConverted::dispatch($transfer, $fromAccount, $toAccount, $exchangeRateFromAndToCurrency);

        return $convertAmount;
    }
}
