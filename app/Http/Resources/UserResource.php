<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);

        return [
            "id": $this->id,
            "username": $this->username,
            "email": $this->email,
            "description": $this->description,
            "kills": $this->kills,
            "deaths":$this->deaths,
            "rank": $this->rank,
            "image": $this->image,
            "wins": $this->wins,
            "losses": $this->losses,
        ];
    }
}
