<?php

namespace App\Http\Resources\Teams;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
class UserTeamResource extends JsonResource
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

        $includeRoles = $request->routeIs('users.index') || $request->routeIs('users.show');
        
        $imageUrl = asset('images/'.$this->image);

        if(env('IMAGE_ENGINE') == 's3'){
            $imageUrl = env('IMAGE_URL') . $this->image;
        }
        return [
            "id" =>  $this->id,
            "username"=> $this->username,
            "kills"=> $this->kills,
            "deaths"=>$this->deaths,
            "rank"=> $this->rank,
            "image"=> $imageUrl,
            "wins"=> $this->wins,
            "losses"=> $this->losses,
            "rank"=> $this->rank,
            'user-win-ratio' => $ratio,
            'user-kd-ratio' => $kdRatio,
        ];
    }
}
