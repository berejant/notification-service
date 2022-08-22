<?php

return [
    'mail'      => (bool)env('NOTIFICATION_MAIL_ENABLE', false),
    'twilio'    => (bool)env('NOTIFICATION_TWILIO_ENABLE', false),
    'telegram'  => (bool)env('NOTIFICATION_TELEGRAM_ENABLE', false),
    'fcm'       => (bool)env('NOTIFICATION_FCM_ENABLE', false),
    'facebook'  => (bool)env('NOTIFICATION_FACEBOOK_ENABLE', false),
];
