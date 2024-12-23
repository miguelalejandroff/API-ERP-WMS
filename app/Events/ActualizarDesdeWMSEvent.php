<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ActualizarDesdeWMSEvent
{
    use Dispatchable, SerializesModels;

    public $pedidosQuery;
    public $ultimoDigito;
     
    public function __construct($pedidosQuery, $ultimoDigito)
    {
        $this->pedidosQuery = $pedidosQuery;
        $this->ultimoDigito = $ultimoDigito;
    }
}
