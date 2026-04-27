<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LedgerEntry extends Model
{
    /** @use HasFactory<\Database\Factories\LedgerEntryFactory> */
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'from_currency_id',
        'to_currency_id',
        'exchange_rate',
        'source_amount',
        'target_amount',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function fromCurrency()
    {
        return $this->belongsTo(Currency::class, 'from_currency_id');
    }

    public function toCurrency()
    {
        return $this->belongsTo(Currency::class, 'to_currency_id');
    }
}
