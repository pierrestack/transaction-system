<?php

namespace App\Models;

use App\Enums\TypeFee;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Fee extends Model
{
    /** @use HasFactory<\Database\Factories\FeeFactory> */
    use HasFactory;

    protected $fillable = [
        'transfer_id',
        'type',
        'amount',
    ];

    protected $casts = [
        'type' => TypeFee::class,
        'amount' => 'decimal:2',
    ];

    public function transfer()
    {
        return $this->belongsTo(Transfer::class);
    }
}
