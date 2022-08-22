<?php

namespace App\Notifications\Channels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;

class FacebookChannel extends \NotificationChannels\Facebook\FacebookChannel
{
    /**
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return array
     * @throws \Throwable
     */
    public function send($notifiable, \Illuminate\Notifications\Notification $notification): array
    {
        try {
            return parent::send($notifiable, $notification);
        } catch (\Throwable $e) {
            app(Dispatcher::class)->dispatch(new NotificationFailed(
                $notifiable, $notification, 'facebook', []
            ));

            throw $e;
        }
    }
}
