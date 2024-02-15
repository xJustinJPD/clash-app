<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;

    protected $fillable = [
        'team_id_1',
        'team_id_2',
        'team_1_score',
        'team_2_score',
        'queue_type',
        'status',
    ];

    public function team1()
    {
        return $this->belongsTo(Team::class, 'team_id_1');
    }

    public function team2()
    {
        return $this->belongsTo(Team::class, 'team_id_2');
    }
    public function gameStats()
    {
        return $this->hasMany(UserTeamGameStats::class);
    }
    
}
