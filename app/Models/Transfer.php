<?php

namespace App\Models;

use App\Enums\StatusTransfer;
use App\Enums\TypeTransfer;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    /** @use HasFactory<\Database\Factories\TransfertFactory> */
    use HasFactory;

    protected $fillable = [
        'token',
        'reference',
        'sender_account_id',
        'receiver_account_id',
        'amount',
        'currency_id',
        'type',
        'status',
        'description',
        'processed_at',
        'expires_at',
    ];

    protected $casts = [
        'amount' => 'float',
        'type' => TypeTransfer::class,
        'status' => StatusTransfer::class,
    ];

    public function senderAccount()
    {
        return $this->belongsTo(Account::class, 'sender_account_id');
    }

    public function receiverAccount()
    {
        return $this->belongsTo(Account::class, 'receiver_account_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    public function fee()
    {
        return $this->hasOne(Fee::class);
    }
}
