<?php

namespace App\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class FinusNotificationCreated implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public function __construct(public readonly int $recipientId, public readonly array $payload)
    {
    }

    public function broadcastOn(): array
    {
        return [new PrivateChannel('finus.user.'.$this->recipientId)];
    }

    public function broadcastAs(): string
    {
        return 'finus.notification';
    }

    public function broadcastWith(): array
    {
        return $this->payload;
    }
}
