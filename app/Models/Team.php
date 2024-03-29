<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image', 'size'];
    protected $guarded = ['wins', 'losses'];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_team');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    public function gamesAsTeam1()
    {
        return $this->hasMany(Game::class, 'team_id_1');
    }

    public function gamesAsTeam2()
    {
        return $this->hasMany(Game::class, 'team_id_2');
    }
    public function gameStats()
    {
        return $this->hasMany(UserTeamGameStats::class);
    }
    
}

