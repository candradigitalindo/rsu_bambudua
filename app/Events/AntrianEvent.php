<?php

namespace App\Events;

use App\Models\Antrian;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $lokasi;
    public function __construct($lokasi)
    {
        $this->lokasi = $lokasi;
    }

    public function broadcastWith()
    {
        $antrian = Antrian::whereDate('created_at', date('Y-m-d'))->where('lokasiloket_id', $this->lokasi)->where('status', 2)->orderBy('updated_at', 'DESC')->first();
        return $antrian;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {

        return [
            new Channel('monitor.umum'),
        ];
    }
}
