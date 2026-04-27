<?php

namespace App\Events;

use App\Models\Account;
use App\Models\ExchangeRate;
use App\Models\Transfer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class TransferConverted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public Transfer $transfer;
    public Account $fromAccount;
    public Account $toAccount;

    public $exchangeRate;

    /**
     * Create a new event instance.
     */
    public function __construct(Transfer $transfer, Account $fromAccount, Account $toAccount, $exchangeRate)
    {
        $this->transfer = $transfer;
        $this->fromAccount = $fromAccount;
        $this->toAccount = $toAccount;
        $this->exchangeRate = $exchangeRate;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
