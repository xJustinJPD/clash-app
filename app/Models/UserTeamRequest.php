<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


class UserTeamRequest extends Model
{
    protected $fillable = ['user_id', 'team_id', 'status'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function team()
    {
        return $this->belongsTo(Team::class);
    }
}

