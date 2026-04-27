<?php

namespace App\Listeners;

use App\Events\TransferConverted;
use App\Models\LedgerEntry;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class StoreFxDetails
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TransferConverted $event): void
    {
        LedgerEntry::create([
            'transfer_id' => $event->transfer->id,
            'from_currency_id' => $event->fromAccount->currency_id,
            'to_currency_id' => $event->toAccount->currency_id,
            'exchange_rate' => $event->exchangeRate->rate,
            'source_amount' => $event->transfer->amount,
            'target_amount' => $event->transfer->amount * $event->exchangeRate->rate,
        ]);
    }
}
