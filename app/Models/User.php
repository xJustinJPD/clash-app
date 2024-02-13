<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

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
        //IF the user doesnt have a role for the specific view show the 401 message
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
        return $this->hasMany(UserTeamGameStat::class);
    }
    
    
   
}
