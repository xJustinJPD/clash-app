<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;

class TeamGameResource extends JsonResource
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
            "image"=> $imageUrl,
            'wins' => $this->wins,
            'rank' => $this->rank,
            'creator' => $this->creator_id,
            'losses' => $this->losses,
            'team-win-ratio' => $ratio,
            
        ];
    }
}
