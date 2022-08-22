<?php

namespace App\Notifications\Channels;

use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Notifications\Events\NotificationFailed;

class MailChannel extends \Illuminate\Notifications\Channels\MailChannel
{
    /**
     * @param $notifiable
     * @param \Illuminate\Notifications\Notification $notification
     * @return void
     * @throws \Throwable
     */
    public function send($notifiable, \Illuminate\Notifications\Notification $notification)
    {
        try {
            parent::send($notifiable, $notification);
        } catch (\Throwable $e) {
            app(Dispatcher::class)->dispatch(new NotificationFailed(
                $notifiable, $notification, 'mail', []
            ));

            throw $e;
        }
    }

}
