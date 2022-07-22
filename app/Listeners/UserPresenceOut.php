<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UserPresenceOut
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
     * @param  Illuminate\Auth\Events\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $user = $event->user;

        $presence = $user->presences()->where('type', 'out')->whereDate('created_at', now()->format('Y-m-d'))->first();
        
        if (!$presence) {
            $user->presences()->create([
                'type' => 'out',
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
        } else {
            $presence->update([
                'time' => now()->format('Y-m-d H:i:s'),
            ]);
        }
    }
}
