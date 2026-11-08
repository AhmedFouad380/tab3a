<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Resend, Postmark, AWS, and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

    'alrajhi' => [
        'base_url' => env('ALRAJHI_BASE_URL', 'https://securepayments.alrajhibank.com.sa/pg/payment/rest/'),
        'tranportal_id' => env('ALRAJHI_TRANPORTAL_ID'),
        'password' => env('ALRAJHI_TRANPORTAL_PASSWORD'),
        'resource_key' => env('ALRAJHI_RESOURCE_KEY'),
        'currency_code' => env('ALRAJHI_CURRENCY_CODE', '682'),
        'mode' => env('ALRAJHI_MODE', 'sandbox'),
    ],

];
