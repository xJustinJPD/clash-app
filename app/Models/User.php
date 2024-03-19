<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass <assignable class=""></assignable>
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'username',
        'email',
        'description',
        'image',
        'kills',
        'deaths',
        'wins',
        'losses',
        'rank',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];
    public function roles()
    {
        return $this->belongsToMany('App\Models\Role', 'user_role');
    }

    public function teams()
    {
        return $this->belongsToMany('App\Models\Team', 'user_team');
    }

    public function authorizeRoles($roles)
    {

        if(is_array($roles)){
            return $this->hasAnyRole($roles) ||
            abort (401, 'This action is unauthorzed');
        }
        return $this->hasRole($roles) ||
        abort(401, 'This action is unauthorized');
    }

    public function hasRole($role)
    {
        return null !== $this->roles()->where('name', $role)->first();
    }

    public function hasAnyRole($roles)
    {
        return null !== $this->roles()->whereIn('name', $roles)->first();
    }
    public function gameStats()
    {
        return $this->hasMany(UserTeamGameStats::class);
    }
    
    public function friends()
    {
        return $this->belongsToMany(User::class, 'friends', 'user_id', 'friend_id')
                    ->orWhere(function ($query) {
                        $query->where('friend_id', $this->id);
                    })
                    ->withPivot('status');
    }

    public function friendRequests()
    {
        return $this->hasMany(Friend::class, 'friend_id')->where('status', 'pending');
    }
    
    public function getAllFriends()
    {
        $userId = $this->id;
        
        //search each friend which is associated through the user then remove any data from the current user to avoid repetition.
        $friends = Friend::where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('friend_id', $userId);
            })
            ->with(['user' => function ($query) use ($userId) {
                $query->where('id', '!=', $userId);
            }, 'friend' => function ($query) use ($userId) {
                $query->where('id', '!=', $userId);
            }])
            ->get();
    
        return $friends;
    }

    public function userTeam()
    {
        return $this->belongsToMany(User::class, 'teams', 'user_id', 'team_id')
                    ->orWhere(function ($query) {
                        $query->where('team_id', $this->id);
                    })
                    ->withPivot('status');
    }

    public function teamRequests()
    {
        return $this->hasMany(UserTeam::class, 'team_id')->where('status', 'pending');
    }
    
    public function getAllTeams()
    {
        $userId = $this->id;
        
        //search each friend which is associated through the user then remove any data from the current user to avoid repetition.
        $teams = Team::where('status', 'accepted')
            ->where(function ($query) use ($userId) {
                $query->where('user_id', $userId)
                    ->orWhere('team_id', $userId);
            })
            ->with(['user' => function ($query) use ($userId) {
                $query->where('id', '!=', $userId);
            }, 'team' => function ($query) use ($userId) {
                $query->where('id', '!=', $userId);
            }])
            ->get();
    
        return $teams;
    }

}

