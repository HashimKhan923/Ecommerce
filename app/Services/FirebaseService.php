<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use ExpoSDK\Expo;
use ExpoSDK\ExpoMessage;

class FirebaseService
{
    // protected static $messaging;

    // /**
    //  * Initialize Firebase Messaging.
    //  */
    // protected static function initialize()
    // {
    //     if (!self::$messaging) {
    //         self::$messaging = (new Factory)
    //             ->withServiceAccount(storage_path('service-account.json'))
    //             ->createMessaging();
    //     }
    // }

    // /**
    //  * Send a notification using Firebase.
    //  */
    // public static function sendNotification($deviceToken, $title, $body)
    // {
    //     self::initialize();

    //     $message = [
    //         'token' => $deviceToken,
    //         'notification' => [
    //             'title' => $title,
    //             'body' => $body,
    //         ]
    //     ];

    //     return self::$messaging->send($message);
    // }

    public static function sendNotification($deviceToken, $title, $body)
    {
        $messages = [
            new ExpoMessage([
                'title' => $title,
                'body' => $body,
            ]),
        ];
        
        /**
         * Users with the below push tokens, will receive the push notification
         */
        $to= $deviceToken;
        
        (new Expo)->send($messages)->to($to)->push();
    }
}
