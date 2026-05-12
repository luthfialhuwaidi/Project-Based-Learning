<?php

namespace App\Events;

use App\Models\Delivery;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DeliveryStatusUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public Delivery $delivery) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('delivery.' . $this->delivery->id),
            new Channel('school.' . $this->delivery->school_id),
            new Channel('public-deliveries'),
        ];
    }

    public function broadcastAs(): string
    {
        return 'status.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery_id' => $this->delivery->id,
            'kode_pengiriman' => $this->delivery->kode_pengiriman,
            'status' => $this->delivery->status,
            'status_label' => $this->delivery->status_label,
            'school_id' => $this->delivery->school_id,
            'school_name' => $this->delivery->school->name,
            'courier_name' => $this->delivery->courier->name,
            'updated_at' => $this->delivery->updated_at->toISOString(),
        ];
    }
}
