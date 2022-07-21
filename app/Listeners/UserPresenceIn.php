<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserPresenceIn
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;

        $presence = $user->presences()->where('type', 'in')->whereDate('time', now()->format('Y-m-d'))->first();
        
        if (!$presence) {
            $user->presences()->create([
                'type' => 'in',
                'datetime' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
