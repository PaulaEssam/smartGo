<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Car;

class CarLocationUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $car;
    public $lat;
    public $lng;

    public function __construct(Car $car, $lat, $lng)
    {
        $this->car = $car;
        $this->lat = $lat;
        $this->lng = $lng;
    }

    public function broadcastOn()
    {
        return new Channel('car.' . $this->car->id);
    }

    public function broadcastWith()
    {
        return [
            'car_id' => $this->car->id,
            'lat' => $this->lat,
            'lng' => $this->lng,
            'timestamp' => now()->toDateTimeString()
        ];
    }
}
