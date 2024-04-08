<?php

namespace App\Http\Resources\Friends;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SentFriendResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  Request  $request
     * @return array<string, mixed>
     */
    public function toArray($request): array
    {
        $friend = User::find($this->friend_id);
        return [
            "id" => $this->id,
            "friend_id" => $this->friend_id,
            "friend_info" => new FriendUserResource($friend),
            "status" => $this->status,
            "created_at" => $this->created_at,
            "updated_at" => $this->updated_at,
        ];
    }
}
