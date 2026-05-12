<?php

namespace App\Events;

use App\Models\Delivery;
use App\Models\DeliveryTracking;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(
        public Delivery $delivery,
        public DeliveryTracking $tracking
    ) {}

    public function broadcastOn(): array
    {
        return [
            new Channel('delivery.' . $this->delivery->id),
            new Channel('school.' . $this->delivery->school_id),
        ];
    }

    public function broadcastAs(): string
    {
        return 'location.updated';
    }

    public function broadcastWith(): array
    {
        return [
            'delivery_id' => $this->delivery->id,
            'kode_pengiriman' => $this->delivery->kode_pengiriman,
            'latitude' => $this->tracking->latitude,
            'longitude' => $this->tracking->longitude,
            'accuracy' => $this->tracking->accuracy,
            'speed' => $this->tracking->speed,
            'recorded_at' => $this->tracking->recorded_at->toISOString(),
            'courier_name' => $this->delivery->courier->name,
            'school_id' => $this->delivery->school_id,
            'status' => $this->delivery->status,
        ];
    }
}
