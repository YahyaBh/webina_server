<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NewUser
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public $id;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function broadcastOn()
    {
        return ['WebIna'];
    }

    public function broadcastAs()
    {
        return 'new-user';
    }
}
