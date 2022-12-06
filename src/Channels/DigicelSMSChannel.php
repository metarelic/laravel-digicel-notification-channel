<?php

namespace Metarelic\Notifications\Channels;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Notifications\Notification;
use Metarelic\Notifications\Messages\DigicelMessage;
use Illuminate\Notifications\Events\NotificationFailed;
use function Exception;

class DigicelSMSChannel
{
    const API_URL = 'https://digicelgroup.api.infobip.com/sms/1/text/single';
    public function __construct(protected string $token, protected string $from) {}
    /**
     * Send the given notification.
     * @param  mixed  $notifiable
     * @param  Notification  $notification
     */
    public function send($notifiable, Notification $notification)
    {
        if (!$to = $notifiable->routeNotificationFor('digicel', $notification)) {
            return;
        }
        $message = $notification->toDigicel($notifiable);

        if (is_string($message)) {
            $message = new DigicelMessage($message);
        }

        $payload = [
            'from' => $message->from ?: $this->from,
            'to' => $to,
            'text' => trim($message->content),
        ];

        $response = Http::withToken($this->token, 'App')->post(static::API_URL, $payload);

        if($response->failed()) {
            $event = new NotificationFailed(
                $notifiable,
                $notification,
                'digicel',
                ['message' => $response->body()]
            );

            $this->events->dispatch($event);

            throw new Exception($response->body());
        }
    }
}