<?php

namespace App\Http\Resources\Stats;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\UserResource;
use App\Http\Resources\Stats\TeamUserResource;
use App\Http\Resources\Games\GameResource;

class UserTeamGameStatsResource extends JsonResource
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
            "id" => $this->id,
            "user_id" => $this->user_id,
			"team_id" => $this->team_id,
			"game_id" => $this->game_id,
			"kills" => $this->kills,
			"deaths" => $this->deaths,
			"created_at" => $this->created_at,
			"updated_at" => $this->updated_at,
            'user' => new UserResource($this->user),
            // 'team' => new TeamGameResource($this->team),
            'game' => new GameResource($this->game),
        ];
    }
}
