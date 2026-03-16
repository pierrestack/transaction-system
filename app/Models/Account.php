<?php

namespace App\Models;

use App\Enums\StatusAccount;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    /** @use HasFactory<\Database\Factories\AccountFactory> */
    use HasFactory;

    protected $fillable = [
        'account_number',
        'balance',
        'currency_id',
        'status',
    ];

    protected $casts = [
        'status' => StatusAccount::class,
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }
}
