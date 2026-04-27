<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ExchangeRate extends Model
{
    /** @use HasFactory<\Database\Factories\ExchangeRateFactory> */
    use HasFactory;

    protected $fillable = [
        'base_currency_id',
        'target_currency_id',
        'rate',
    ];

    public function BaseCurrency()
    {
        return $this->belongsTo(Currency::class, 'base_currency_id');
    }

    public function TargetCurrency()
    {
        return $this->belongsTo(Currency::class, 'target_currency_id');
    }

    public function getExchangeRateFromAndToCurrency(float $fromCurrencyId, float $toCurrencyId): ?object
    {
        return DB::table('exchange_rates')
            ->where('base_currency_id', $toCurrencyId)
            ->where('target_currency_id', $fromCurrencyId)
            ->orderByDesc('created_at')
            ->first();
    }
}
