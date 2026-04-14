<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Subscription Plans
    |--------------------------------------------------------------------------
    */
    'plans' => [
        'free' => [
            'label'          => 'Free',
            'post_quota'     => 5,
            'account_limit'  => 1,
            'amount_paise'   => 0,
        ],
        'starter' => [
            'label'          => 'Starter',
            'post_quota'     => 30,
            'account_limit'  => 1,
            'amount_paise'   => 39900,   // Rs. 399
        ],
        'growth' => [
            'label'          => 'Growth',
            'post_quota'     => 150,
            'account_limit'  => 3,
            'amount_paise'   => 89900,   // Rs. 899
        ],
        'agency' => [
            'label'          => 'Agency',
            'post_quota'     => 999999,  // unlimited
            'account_limit'  => 10,
            'amount_paise'   => 249900,  // Rs. 2,499
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Meta Graph API
    |--------------------------------------------------------------------------
    */
    'meta' => [
        'graph_version' => 'v19.0',
        'graph_url'     => 'https://graph.facebook.com/v19.0',
        'app_id'        => env('META_APP_ID'),
        'app_secret'    => env('META_APP_SECRET'),
        'redirect_uri'  => env('META_REDIRECT_URI'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Post Statuses
    |--------------------------------------------------------------------------
    */
    'statuses' => ['draft', 'scheduled', 'publishing', 'published', 'failed'],

];
