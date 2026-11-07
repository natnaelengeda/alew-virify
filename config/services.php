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

    'cbe' => [
        'base_url' => env('CBE_BASE_URL', 'https://mb.cbe.com.et'),
        'app_id' => env('CBE_APP_ID', 'd1292e42-7400-49de-a2d3-9731caa4c819'),
        'app_version' => env('CBE_APP_VERSION', '0a01980b-9859-1369-8198-59f403820000'),
        'cookie' => env('CBE_COOKIE'),
        'timeout' => env('CBE_TIMEOUT', 15),
    ],

    'telebirr' => [
        'base_url' => env('TELEBIRR_BASE_URL', 'https://transactioninfo.ethiotelecom.et'),
        'timeout' => env('TELEBIRR_TIMEOUT', 20),
        'user_agent' => env(
            'TELEBIRR_USER_AGENT',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 18_5 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/18.5 Mobile/15E148 Safari/604.1',
        ),
    ],

];
