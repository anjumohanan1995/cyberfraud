<?php

return [
    'default' => 'smtp',

    'mailers' => [
        'smtp' => [
            'transport' => 'smtp',
            'host' => env('MAIL_HOST', 'smtp.mailgun.org'),
            'port' => env('MAIL_PORT', 587),
            'encryption' => env('MAIL_ENCRYPTION', 'tls'),
            'username' => env('MAIL_USERNAME'),
            'password' => env('MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
        ],

        'aravind' => [
            'transport' => 'smtp',
            'host' => env('ARAVIND_MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('ARAVIND_MAIL_PORT', 587),
            'encryption' => env('ARAVIND_MAIL_ENCRYPTION', 'tls'),
            'username' => env('ARAVIND_MAIL_USERNAME'),
            'password' => env('ARAVIND_MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            'from' => [
                'address' => env('ARAVIND_MAIL_FROM_ADDRESS', 'info@example.com'),
                'name' => env('ARAVIND_MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'rajmohan' => [
            'transport' => 'smtp',
            'host' => env('RAJMOHAN_MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('RAJMOHAN_MAIL_PORT', 587),
            'encryption' => env('RAJMOHAN_MAIL_ENCRYPTION', 'tls'),
            'username' => env('RAJMOHAN_MAIL_USERNAME'),
            'password' => env('RAJMOHAN_MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            'from' => [
                'address' => env('RAJMOHAN_MAIL_FROM_ADDRESS', 'info@example.com'),
                'name' => env('RAJMOHAN_MAIL_FROM_NAME', 'Example'),
            ],
        ],

        'sreejith' => [
            'transport' => 'smtp',
            'host' => env('SREEJITH_MAIL_HOST', 'smtp.gmail.com'),
            'port' => env('SREEJITH_MAIL_PORT', 587),
            'encryption' => env('SREEJITH_MAIL_ENCRYPTION', 'tls'),
            'username' => env('SREEJITH_MAIL_USERNAME'),
            'password' => env('SREEJITH_MAIL_PASSWORD'),
            'timeout' => null,
            'auth_mode' => null,
            'from' => [
                'address' => env('SREEJITH_MAIL_FROM_ADDRESS', 'info@example.com'),
                'name' => env('SREEJITH_MAIL_FROM_NAME', 'Example'),
            ],
        ],
    ],

    'from' => [
        'address' => '', // Leave this empty since we are dynamically setting 'from' address
        'name' => '',    // Leave this empty since we are dynamically setting 'from' name
    ],

    'markdown' => [
        'theme' => 'default',
        'paths' => [resource_path('views/vendor/mail')],
    ],
];
