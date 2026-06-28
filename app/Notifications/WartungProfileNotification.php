<?php

namespace App\Notifications;

use App\Models\WartungProfile;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\DatabaseMessage;
use Illuminate\Notifications\Notification;

class WartungProfileNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected WartungProfile $profile;
    protected string $event; // created, updated, deleted

    public function __construct(WartungProfile $profile, string $event)
    {
        $this->profile = $profile;
        $this->event   = $event;
    }

    public function via($notifiable)
    {
        return ['database'];
    }

    public function toDatabase($notifiable): DatabaseMessage
    {
        return new DatabaseMessage([
            'type'       => 'wartung_profile',
            'event'      => $this->event,
            'profile_id' => $this->profile->id,
            'name'       => $this->profile->name,
            'slug'       => $this->profile->slug,
            'product_id' => $this->profile->product_id,
        ]);
    }
}
