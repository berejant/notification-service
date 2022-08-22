<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\AnonymousNotifiable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use NotificationChannels\Facebook\FacebookMessage;
use NotificationChannels\Fcm\FcmMessage;
use NotificationChannels\Telegram\TelegramMessage;
use NotificationChannels\Twilio\TwilioSmsMessage;

abstract class BaseNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public int $tries = 1;

    const TRANSLATION_PREFIX = 'notification';

    protected array $variables;

    protected array $failoverChannels;

    public function via(AnonymousNotifiable $notifiable)
    {
        return array_keys($notifiable->routes);
    }

    public function shouldSend($notifiable, $channel): bool {
        return config('notificationChannels.' . $channel) === true;
    }

    /**
     * @return array
     */
    public function getFailoverChannels(): array
    {
        return $this->failoverChannels ?? [];
    }

    /**
     * @param array $failoverChannels
     */
    public function setFailoverChannels(array $failoverChannels): void
    {
        $this->failoverChannels = $failoverChannels;
    }

    /**
     * @return array
     */
    public function getVariables(): array
    {
        return $this->variables ?? [];
    }

    /**
     * @param array $variables
     */
    public function setVariables(array $variables): void
    {
        $this->variables = $variables;
    }

    public function getType(): string {
        return substr(strrchr(get_class($this), '\\'), 1);
    }

    protected function getTrans($channel, $key) {
        $prefix = self::TRANSLATION_PREFIX . $this->getType() . '.';

        $keyForChannel = $prefix . $channel . '.' . $key;
        $keyCommon = $prefix . $key;

        /** @var \Illuminate\Contracts\Translation\Translator $translator */
        $translator = app('translator');

        $translationKey = $translator->hasForLocale($keyForChannel, $this->locale) ? $keyForChannel : $keyCommon;

        return $translator->get($translationKey, $this->getVariables(), $this->locale, false);
    }

    public function fcmProject($notifiable, $message)
    {
        // $message is what is returned by `toFcm`
        return 'app'; // name of the firebase project to use
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject($this->getTrans('mail', 'title'))
            ->line($this->getTrans('mail', 'message'));
    }


    public function toTwilio($notifiable)
    {
        return (new TwilioSmsMessage())
            ->content($this->getTrans('twilio', 'message'));
    }

    public function toTelegram($notifiable)
    {
        return TelegramMessage::create()
            // Markdown supported.
            ->content($this->getTrans('telegram', 'message'));
    }


    public function toFacebook($notifiable)
    {
        return FacebookMessage::create()
            ->text($this->getTrans('facebook', 'message'));
    }


    public function toFcm($notifiable)
    {
        return FcmMessage::create()
            ->setNotification(
                \NotificationChannels\Fcm\Resources\Notification::create()
                ->setTitle($this->getTrans('twilio', 'title'))
                ->setBody($this->getTrans('twilio', 'message'))
            );
    }
}
