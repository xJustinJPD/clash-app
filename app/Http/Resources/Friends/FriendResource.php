<?php

namespace App\Http\Resources\Friends;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $friendUserResource = new FriendUserResource($this->user ?? $this->friend);

        return [
            "id" =>  $this->id,
            'friend' => $friendUserResource,
        ];
    }
}
