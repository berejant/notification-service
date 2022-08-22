<?php

namespace App\Listeners;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Events\NotificationFailed;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class SendNotificationToFailoverChannel implements ShouldQueue
{
    protected Dispatcher $dispatcher;

    public function __construct(Dispatcher $dispatcher) {
        $this->dispatcher = $dispatcher;
    }

    /**
     * Handle the event.
     *
     * @param NotificationFailed $event
     * @return void
     */
    public function handle(NotificationFailed $event): void
    {
        $notification = $event->notification;
        if (!($notification instanceof BaseNotification)) {
            return;
        }

        $failoverChannels = $notification->getFailoverChannels();
        if (!$failoverChannels) {
            return;
        }
        $nextChannel = array_shift($failoverChannels);
        $notification->setFailoverChannels($failoverChannels);

        Log::info('failover to next channel', $nextChannel);
        $notifiable = new AnonymousNotifiable;
        $notifiable->route($nextChannel['name'], $nextChannel['route']);

        $this->dispatcher->send($notifiable, $notification);
    }
}
