<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

use App\Http\Requests\CreateNotificationRequest;
use App\Notifications\BaseNotification;
use Illuminate\Notifications\AnonymousNotifiable;
use \Illuminate\Contracts\Notifications\Dispatcher;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    public function sendNotification (Dispatcher $dispatcher, CreateNotificationRequest $request): \Illuminate\Http\JsonResponse
    {

    /** @var BaseNotification $notification */
    $notification = new ('App\Notifications\\' . $request->get('type'));
    $notifiable = new AnonymousNotifiable;

    $notification->locale($request->get('locale', 'en'));

    $channelsChainStrategy = $request->get('channelsChainStrategy', 'multi');
    $channels = $request->get('channels');

    // filter out disabled channels
    $channels = array_filter($channels, function ($channel) use ($notification, $notifiable) {
        return $notification->shouldSend($notifiable, $channel['name']);
    });

    if (!$channels) {
        return response()->json([
            'status' => false,
            'error' => 'All channels disabled',
        ], 500);
    }

    $firstChannel = array_shift($channels);
    $notifiable->route($firstChannel['name'], $firstChannel['route']);

    if ($channelsChainStrategy === 'multi') {
        foreach ($channels as $channel) {
            $notifiable->route($channel['name'], $channel['route']);
        }

    } elseif ($channelsChainStrategy === 'failover') {
        $notification->setFailoverChannels($channels);
    }

    $variables = $request->get('variables');
    if ($variables) {
        $notification->setVariables($variables);
    }

    $dispatcher->send($notifiable, $notification);

    return response()->json([
        'status' => true
    ]);
}
}
