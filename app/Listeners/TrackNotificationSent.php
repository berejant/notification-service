<?php

namespace App\Listeners;

use App\Models\SentNotification;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Events\NotificationSent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Log;

class TrackNotificationSent implements ShouldQueue
{
    /**
     * Handle the event.
     *
     * @param NotificationSent $event
     * @return void
     */
    public function handle(NotificationSent $event): void
    {
        $notification = $event->notification;
        if (!($notification instanceof BaseNotification)) {
            return;
        }

        /** @var AnonymousNotifiable $notifiable */
        $notifiable = $event->notifiable;

        $sentNotification = new SentNotification();

        $sentNotification->id = $notification->id;
        $sentNotification->type = $notification->getType();
        $sentNotification->channel = $event->channel;
        $sentNotification->route = (string)$notifiable->routeNotificationFor($event->channel, $notification);
        $sentNotification->locale = $notification->locale;
        $sentNotification->variables = $notification->getVariables();
        $sentNotification->created_at = Date::now();

        try {
            $sentNotification->save();
        } catch (\Throwable $e) {
            var_dump($e);exit;
        }
    }
}
