<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Teams\TeamResource;
class UserResource extends JsonResource
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

        $imageUrl = asset('images/'.$this->image);

        if(env('IMAGE_ENGINE') == 's3'){
            $imageUrl = env('IMAGE_URL') . $this->image;
        }
        return [
            "id" =>  $this->id,
            "username"=> $this->username,
            "email"=> $this->email,
            "description"=> $this->description,
            "kills"=> $this->kills,
            "deaths"=>$this->deaths,
            "rank"=> $this->rank,
            "image"=> $imageUrl,
            "wins"=> $this->wins,
            "losses"=> $this->losses,
            'user-win-ratio' => $ratio,
            'user-kd-ratio' => $kdRatio,
            "imageFormal" => $this->image,
            'teams' => TeamResource::collection($this->whenLoaded('teams')), 
        ];
    }
}
