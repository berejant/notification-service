<?php

namespace App\Providers;

use Illuminate\Notifications\ChannelManager;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\ServiceProvider;
use App\Notifications\Channels\FacebookChannel;
use NotificationChannels\Fcm\FcmChannel;
use NotificationChannels\Telegram\TelegramChannel;
use NotificationChannels\Twilio\TwilioChannel;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->bind(
            \Illuminate\Notifications\Channels\MailChannel::class,
            \App\Notifications\Channels\MailChannel::class
        );

        if ($this->app->isLocal()) {
            $this->app->register(\Barryvdh\LaravelIdeHelper\IdeHelperServiceProvider::class);
        }

        Notification::resolved(static function (ChannelManager $service) {
            $service->extend('telegram', static function ($app) {
                return $app->make(TelegramChannel::class);
            });

            $service->extend('fcm', static function ($app) {
                return $app->make(FcmChannel::class);
            });

            $service->extend('twilio', static function ($app) {
                return $app->make(TwilioChannel::class);
            });

            $service->extend('facebook', static function ($app) {
                return $app->make(FacebookChannel::class);
            });
        });
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

    }
}
