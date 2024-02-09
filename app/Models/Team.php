<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Team extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'image'];
    protected $guarded = ['wins', 'losses'];

    public function users()
    {
        return $this->belongsToMany('App\Models\User', 'user_team');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
    
}

