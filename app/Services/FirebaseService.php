<?php

namespace App\Services;

use Kreait\Firebase\Factory;

class FirebaseService
{
    protected static $messaging;

    /**
     * Initialize Firebase Messaging.
     */
    protected static function initialize()
    {
        if (!self::$messaging) {
            self::$messaging = (new Factory)
                ->withServiceAccount(storage_path('service-account.json'))
                ->createMessaging();
        }
    }

    /**
     * Send a notification using Firebase.
     */
    public static function sendNotification($deviceToken, $title, $body)
    {
        self::initialize();

        $message = [
            'token' => $deviceToken,
            'notification' => [
                'title' => $title,
                'body' => $body,
            ]
        ];

        return self::$messaging->send($message);
    }
}
