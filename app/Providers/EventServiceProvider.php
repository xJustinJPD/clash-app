<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\GameCreated;
use App\Events\GameUpdated;
use App\Events\FriendRequestReceived;
use App\Listeners\ProcessGameStats;
use App\Events\UserInvitedToTeam;
use App\Listeners\HandleUserTeamInvitation;




class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        GameCreated::class => [
            ProcessGameStats::class,
        ],
       UserInvitedToTeam::class => [
        HandleUserTeamInvitation::class,
       ]
        
    ];

    /**
     * Register any events for your application.
     */
    public function boot(): void
    {
        //
    }

    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
