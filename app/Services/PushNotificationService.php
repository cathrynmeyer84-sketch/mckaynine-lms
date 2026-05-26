<?php

namespace App\Services;

use App\Models\{PushSubscription, User};
use Minishlink\WebPush\{WebPush, Subscription, VAPID};

class PushNotificationService
{
    private WebPush $webPush;

    public function __construct()
    {
        $auth = [
            'VAPID' => [
                'subject'    => config('webpush.vapid.subject'),
                'publicKey'  => config('webpush.vapid.public_key'),
                'privateKey' => config('webpush.vapid.private_key'),
            ],
        ];

        $this->webPush = new WebPush($auth);
        $this->webPush->setReuseVAPIDHeaders(true);
    }

    public function sendToUser(User $user, string $title, string $body, array $data = []): void
    {
        $subscriptions = PushSubscription::where('user_id', $user->id)->get();

        foreach ($subscriptions as $sub) {
            $this->queueNotification($sub, $title, $body, $data);
        }

        $this->flush($subscriptions->pluck('id')->toArray());
    }

    public function sendToUsers(iterable $users, string $title, string $body, array $data = []): void
    {
        $subIds = [];

        foreach ($users as $user) {
            $subs = PushSubscription::where('user_id', $user->id)->get();
            foreach ($subs as $sub) {
                $this->queueNotification($sub, $title, $body, $data);
                $subIds[] = $sub->id;
            }
        }

        $this->flush($subIds);
    }

    private function queueNotification(PushSubscription $sub, string $title, string $body, array $data): void
    {
        $payload = json_encode([
            'title' => $title,
            'body'  => $body,
            'icon'  => '/icons/icon-192.png',
            'badge' => '/icons/badge-72.png',
            'data'  => $data,
        ]);

        $subscription = Subscription::create([
            'endpoint'        => $sub->endpoint,
            'publicKey'       => $sub->public_key,
            'authToken'       => $sub->auth_token,
            'contentEncoding' => $sub->content_encoding,
        ]);

        $this->webPush->queueNotification($subscription, $payload);
    }

    private function flush(array $subIds): void
    {
        foreach ($this->webPush->flush() as $report) {
            // Remove expired/invalid subscriptions
            if (!$report->isSuccess()) {
                $endpoint = $report->getRequest()->getUri()->__toString();
                PushSubscription::where('endpoint', $endpoint)->delete();
            }
        }
    }
}
