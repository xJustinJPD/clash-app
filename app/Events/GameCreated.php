<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Game;

class GameCreated
{
    use Dispatchable, SerializesModels;

    public $game;

    public function __construct(Game $game)
    {
        $this->game = $game;
    }
}
