<?php

namespace App\Http\Resources\Teams;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Teams\UserTeamResource;

class TeamResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray($request)
    {
        // Calculate ratio
        $totalGames = $this->wins + $this->losses;
        $ratio = ($totalGames != 0) ? intval($this->wins / $totalGames * 100) : 0;

        $imageUrl = asset('images/'.$this->image);

        if(env('IMAGE_ENGINE') == 's3'){
            $imageUrl = env('IMAGE_URL') . $this->image;
        }

        return [
            'id' => $this->id,
            'name' => $this->name,
            'size' => $this->size,
            "image"=> $imageUrl,
            'wins' => $this->wins,
            'losses' => $this->losses,
            'rank' => $this->rank,
            'users' => UserTeamResource::collection($this->users),
            'team-win-ratio' => $ratio,
            'creator' =>$this->creator_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            "imageFormal" => $this->image
        ];
    }
}
