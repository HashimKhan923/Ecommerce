<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'mailgun' => [
        'domain' => env('MAILGUN_DOMAIN'),
        'secret' => env('MAILGUN_SECRET'),
        'endpoint' => env('MAILGUN_ENDPOINT', 'api.mailgun.net'),
        'scheme' => 'https',
    ],

    'postmark' => [
        'token' => env('POSTMARK_TOKEN'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'stripe' => [
        'key' => 'pk_live_51NiN6ZBn8IlFKsgyLkTQUuad9S06TXXV7rZ95Iuuiy1iQkYK27OLc9TaXu9gz2tFnFwmZs62giktyTKxEo5Gatco00REWd2ffQ',
        'secret' => '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',
        
    ],


        'google' => [
            'client_id' => '400530329929-hlp4g1d951orjf69l7nauf90c7qlqvdr.apps.googleusercontent.com',
            'client_secret' => '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',
            'redirect' => 'https://api.dragonautomart.com/api/login/google/callback',
        ],
    

];
