# Notification Service

It is example of Notification Service based on Laravel framework according to requirements in offline task.

## Key Features
 - JSON API for send Notification `/send`.
 - Multi channels (providers) for deliver Notification. This package include next providers:
   - [Mail](https://laravel.com/docs/9.x/mail)
   - [FCM](https://laravel-notification-channels.com/fcm/) (for Android and iOs platforms)
   - SMS by [Twilio](https://laravel-notification-channels.com/twilio/)
   - [Telegram](https://laravel-notification-channels.com/telegram/)
   - [Facebook](https://laravel-notification-channels.com/facebook/)
 - Notification providers can be extended by extra packages from [Laravel Notification Channels](https://laravel-notification-channels.com/). Particularly, different  Push providers, Discord, Twitter, SMS Services, Webhook etc
 - Two strategy of sending Notification: `multi` and `failover`.
   - With `multi` - Notification will be sent to all channel in list.
   - With `failback` - Attempt to sent notification to each channel in list until successful one. Send only one Notification to the first Provider who accepted it for sending.
 - Localization based on [Laravel Localization](https://laravel.com/docs/9.x/localization). You can specify translation per Notification and Channel
 - Custom mail driver. Laravel support different [mail drivers](): SMTP, Amazon SES, Mailgun, sendmail. Also have mail driver failover option. You can find [Laravel Mail Drivers](https://packagist.org/?query=laravel%20mail%20driver&tags=laravel%20mail%20driver~mail) for popular services
 - Each successful notification sent is tracked in table `sent_notifications`
 - Scaling based on [Laravel Queue](https://laravel.com/docs/9.x/queues). For example, this package includes Beanstalk, but easily can be changed to any other: AWS SQS, Rabbit, Redis etc.
 - Email template can be customized with [Blade](https://laravel.com/docs/9.x/blade)


### Strategy Failback and  channel switch
If external service got error during delivering Notification it is not handled by this Service. It related on async Notification processing on provider side. For example: 
 - SMS sent success but not delivered in XX minutes;
 - Mail sent but receive Mail Daemon Error;
 - FCM send but device offline or unsubscribed.

Handling delivery errors requires additional callbacks from providers or getting delivery error reports. This package doesn't include it. The package handles only the next states:
 - "accept for sending" - then nothing to do and not send Notification to another channel.
 - "failed to add for sending" (include: "provider goes down") - then try to send to next channel.

## Requirements
Docker, Composer.

## Build

For get working application with Docker execute
> ./build.sh

It run composer install, build docker container and run init SQL migration.

then edit .env
> nano .env

You need to enable/disable Notification chanells:
```
NOTIFICATION_MAIL_ENABLE=true
NOTIFICATION_TWILIO_ENABLE=true
NOTIFICATION_TELEGRAM_ENABLE=true
NOTIFICATION_FCM_ENABLE=true
NOTIFICATION_FACEBOOK_ENABLE=true
```

setup Credentials for Notification Services (Amazon, Google FCM, Twilio, Telegram etc)
For test purpose you can set "log" as Mail driver - then you can see sent email in `storage/logs/mail.log`
> MAIL_MAILER=log

For production, it is recommended to use real Mail drivers such as `ses`, `smtp` etc.

## Run application
> docker compose up -d

## Examples of request

Single Notification:
```bash
curl -X POST "http://localhost:8092/send" -H "Content-Type: application/json"  -H "Accept: application/json" --data '{
  "type":"welcome",
  "locale": "fr",
  "channels":[
    {
      "name":"mail",
      "route":"123@example.com"
    }
  ],
  "variables": {
      "name": "John"
  }
}'
```

Multi Notification to multiple channel:
```bash
curl -X POST "http://localhost:8092/send" -H "Content-Type: application/json" -H "Accept: application/json" --data '{
  "type":"welcome",
  "locale": "en",
  "channelsChainStrategy": "multi",
  "channels":[
    {
      "name":"mail",
      "route":"123@example.com"
    },
    {
       "name": "fcm",
       "route":"test"
     }
  ],
  "variables": {
      "name": "John"
  }
}'
```


Notification with failover channels:
```bash
curl -X POST "http://localhost:8092/send" -H "Content-Type: application/json" -H "Accept: application/json" --data '{
  "type":"welcome",
  "locale": "en",
  "channelsChainStrategy": "failover",
  "channels":[
    {
      "name":"telegram",
      "route":"123@example.com"
    },
    {
       "name": "twilio",
       "route":"test"
     },
    {
      "name":"facebook",
      "route":"123@example.com"
    },
    {
      "name":"mail",
      "route":"123@example.com"
    }
  ],
  "variables": {
      "name": "John"
  }
}'
```

## Review tips
 - Send endpoint Controller [app/Http/Controllers/Controller.php](app/Http/Controllers/Controller.php)
 - Channel Failback - [app/Listeners/SendNotificationToFailoverChannel.php](app/Listeners/SendNotificationToFailoverChannel.php)
 - Track sending in DB - [app/Listeners/TrackNotificationSent.php](app/Listeners/TrackNotificationSent.php)
 - Localization - see [lang/](lang/) dir
 - Endpoint `/send` almost will return `true`. See `storage/logs/laravel.log` for errors in queued jobs.
 - With option `MAIL_MAILER=log` you can see outbound emails in `storage/logs/mail.log`
 - Email template - [resources/views/vendor/notifications/email.blade.php](resources/views/vendor/notifications/email.blade.php)


## To do
 - Add tests
  - Improve error handling
  - Handle Provider delivery error (async callback or poll for delivering status)
      - Route is invalid: token is expired, email or phone is not active, etc
      - Wrong Client Credentials
      - Transport or timeout errors
  - Optional. Store metrics in Time Series Database such as: InfluxDB, Prometheus or Clickhouse
  - Add Grafana for having an analytics dashboard based on Time Series DB or table `sent_notifications`
