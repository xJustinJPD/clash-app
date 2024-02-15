<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class TeamResource extends JsonResource
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
            'id' => $this->id,
			'name' => $this->name,
			'size' => $this->size,
			'image' => $this->image,
			'wins' => $this->wins,
			'losses' => $this->losses,
            'users' => UserResource::collection($this->users),
            // 'ratio' => ($this->wins/$this->loses)*100,
			// 'creator' =>$this->creator,
			'created_at' => $this->created_at,
			'updated_at' => $this->updated_at,
        ];
    }
}
