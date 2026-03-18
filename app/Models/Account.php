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

    public function sentTransfers()
    {
        return $this->hasMany(Transfer::class, 'sender_account_id');
    }

    public function receivedTransfers()
    {
        return $this->hasMany(Transfer::class, 'receiver_account_id');
    }

    public function operations()
    {
        return $this->hasMany(Operation::class);
    }
}
