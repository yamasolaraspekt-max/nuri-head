<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\DB;

class LogUserLogout
{
    /**
     * Handle the event.
     */
    public function handle(Logout $event)
    {
        $user = $event->user;
        $user->update(['is_active' => 0]); // Set the user as inactive
    }
}
