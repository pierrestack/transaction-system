<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    /** @use HasFactory<\Database\Factories\CurrencyFactory> */
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
        'symbol',
    ];

    public function exchangeRatesBase()
    {
        return $this->hasMany(ExchangeRate::class, 'base_currency_id');
    }

    public function exchangeRatesTarget()
    {
        return $this->hasMany(ExchangeRate::class, 'target_currency_id');
    }

    public function accounts()
    {
        return $this->hasMany(Account::class);
    }
}
