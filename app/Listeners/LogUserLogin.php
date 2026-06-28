<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Auth\Events\Login;
use Illuminate\Support\Facades\DB;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
   public function handle(Login $event)
    {
        $user = $event->user;
        $user->update(['is_active' => 1]); // Set the user as active
        session(['user_session_' . $user->id => session()->getId()]);

    }
}
