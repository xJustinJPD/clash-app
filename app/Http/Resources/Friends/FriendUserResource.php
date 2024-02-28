<?php

namespace App\Http\Resources\Friends;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendUserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $totalGames = $this->wins + $this->losses;
        $ratio = ($totalGames != 0) ? intval($this->wins / $totalGames * 100) : 0;

        $kdRatio = ($this->deaths != 0) ? ($this->kills / $this->deaths) : $this->kills;

        return [
            "id" =>  $this->id,
            "username"=> $this->username,
            "rank"=> $this->rank ?? null,
            "image"=> $this->image ?? null,
            'user-win-ratio' => $ratio,
            'user-kd-ratio' => $kdRatio,
        ];
    }
}
