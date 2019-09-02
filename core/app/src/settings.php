<?php
return [
    'settings' => [
        'displayErrorDetails' => true, // set to false in production
        'addContentLengthHeader' => false, // Allow the web server to send the content-length header

        // general app settings
        'app' => [
            'name' => 'xOrga',
            'debug' => true,
            'frontendBaseUrl' => '*'
        ],

        // db
        'db' => [
            'user' => '',
            'password' => '',
            'dsn' => '',
            'freeze' => false // set to true in production
        ],

        // jwt
        'jwt' => [
            'secret' => '',
            'algorithm' => ['HS512'],
            'ttl' => 7 * 24 * 3600,
            'ttlShort' => 20 * 60,
            'secure' => false
        ],

        // mail client
        'emailService' => [
            'smtpDebug' => 0, // 2 = debug; 0 = production;
            'host' => '',
            'port' => 587,
            'user' => '',
            'password' => '',
            'fromMail' => '',
            'fromName' => 'xOrga'
        ]
    ]
];
