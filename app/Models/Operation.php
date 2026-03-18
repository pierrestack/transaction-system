<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Operation extends Model
{
    /** @use HasFactory<\Database\Factories\OperationFactory> */
    use HasFactory;

    protected $fillable = [
        'account_id',
        'transfer_id',
        'type',
        'amount',
        'balance_before',
        'balance_after'
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }

    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
