<?php

namespace App\Http\Resources\Games;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
// use App\Http\Resources\TeamResource;
use App\Http\Resources\Games\TeamGameResource;

class GameResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // return parent::toArray($request);
        $imageUrl1 = asset('images/'.$this->team_1_image);
        $imageUrl2 = asset('images/'.$this->team_2_image);

        if(env('IMAGE_ENGINE') == 's3'){
            $imageUrl1 = env('IMAGE_URL') . $this->team_1_image;
            $imageUrl2 = env('IMAGE_URL') . $this->team_2_image;
        }
        return [
            "id" => $this->id,
			"team_id_1" => $this->team_id_1,
			"team_id_2" => $this->team_id_2,
			"team_1_score" => $this->team_1_score,
			"team_2_score" => $this->team_2_score,
			"team_1_result" => $this->team_1_result,
			"team_2_result" => $this->team_2_result,
            "team_1_image"=> $imageUrl1,
            "team_2_image"=> $imageUrl2,
			"queue_type" => $this->queue_type,
			"status" => $this->status,
            "team_1" => new TeamGameResource($this->team1),
            "team_2" => new TeamGameResource($this->team2),
			"created_at" => $this->created_at,
			// "updated_at" => $this->updated_at,
        ];
    }
}
