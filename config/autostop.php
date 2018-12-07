<?php
return [
    'google' => [
        'maps' => [
            'apikey' => env('GOOGLE_MAP_API_KEY', ''),
        ],
        'firebase' => [
            'apiKey' => env('GOOGLE_FB_API_KEY', ''),
            'authDomain' => env('GOOGLE_FB_AUTH_DOMAIN', ''),
            'databaseURL' => env('GOOGLE_FB_DB_URL', ''),
            'projectId' => env('GOOGLE_FB_PROJECT_ID', ''),
            'storageBucket' => env('GOOGLE_FB_STORAGE_BUCKET', ''),
            'messagingSenderId' => env('GOOGLE_FB_MESSAGING_SENDER_ID', ''),
        ]
    ]
];