<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\RealtimeUserNotification;

class RealtimeNotificationDebugController extends Controller
{
    /**
     * Send a demo notification to the logged-in user.
     */
    public function demo(Request $request)
    {
        $user = $request->user();

        $payload = [
            'type'         => 'demo',
            'title'        => 'Demo-Benachrichtigung',
            'message'      => 'Das ist eine Realtime-Demo über Laravel Reverb.',
            'performed_at' => now()->toDateTimeString(),
        ];

        $user->notify(new RealtimeUserNotification($payload));

        return response()->json(['status' => 'sent']);
    }
}
