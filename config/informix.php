<?php

return [

    'informix' => [
        'driver'          => 'informix',
        'host'            => env('DB_IFX_HOST', 'localhost'),
        'database'        => env('DB_IFX_DATABASE', 'forge'),
        'username'        => env('DB_IFX_USERNAME', 'forge'),
        'password'        => env('DB_IFX_PASSWORD', ''),
        'service'         => env('DB_IFX_SERVICE', '1525'),
        'server'          => env('DB_IFX_SERVER', ''),
        'db_locale'       => 'en_US.819',
        'client_locale'   => 'en_US.819',
        'client_encoding' => 'UTF-8',
        'db_encoding'     => 'UTF-8',
        'protocol'        => 'onsoctcp',
        'initSqls'        => false,
        'prefix'          => '',
        'charset'         => 'utf8',
        'collation'       => 'utf8_unicode_ci',
        'enable_scroll'   => 1,
        'options'         => [
            'default' => 'informix',
            PDO::ATTR_PERSISTENT => true,
            PDO::FETCH_ASSOC     => true,
            PDO::ATTR_CASE       => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT    => env('DB_IFX_TIMEOUT', '15'),
        ],
    ],

    'informix_dev' => [
        'driver'          => 'informix',
        'host'            => env('DB_IFX_DEV_HOST', 'localhost'),
        'database'        => env('DB_IFX_DEV_DATABASE', 'forge'),
        'username'        => env('DB_IFX_DEV_USERNAME', 'forge'),
        'password'        => env('DB_IFX_DEV_PASSWORD', ''),
        'service'         => env('DB_IFX_DEV_SERVICE', '1525'),
        'server'          => env('DB_IFX_DEV_SERVER', ''),
        'db_locale'       => 'en_US.819',
        'client_locale'   => 'en_US.819',
        'client_encoding' => 'UTF-8',
        'db_encoding'     => 'UTF-8',
        'protocol'        => 'onsoctcp',
        'initSqls'        => false,
        'prefix'          => '',
        'charset'         => 'utf8',
        'collation'       => 'utf8_unicode_ci',
        'enable_scroll'   => 1,
        'options'         => [
            PDO::ATTR_PERSISTENT => true,
            PDO::FETCH_ASSOC     => true,
            PDO::ATTR_CASE       => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT    => env('DB_IFX_TIMEOUT', '15'),
        ],
    ],

    'informix_dev_157' => [
        'driver'          => 'informix',
        'host'            => env('DB_IFX_DEV_HOST_157', 'localhost'),
        'database'        => env('DB_IFX_DEV_DATABASE_157', 'forge'),
        'username'        => env('DB_IFX_DEV_USERNAME_157', 'forge'),
        'password'        => env('DB_IFX_DEV_PASSWORD_157', ''),
        'service'         => env('DB_IFX_DEV_SERVICE_157', '1525'),
        'server'          => env('DB_IFX_DEV_SERVER_157', ''),
        'db_locale'       => 'en_US.819',
        'client_locale'   => 'en_US.819',
        'client_encoding' => 'UTF-8',
        'db_encoding'     => 'UTF-8',
        'protocol'        => 'onsoctcp',
        'initSqls'        => false,
        'prefix'          => '',
        'charset'         => 'utf8',
        'collation'       => 'utf8_unicode_ci',
        'enable_scroll'   => 1,
        'options'         => [
            PDO::ATTR_PERSISTENT => true,
            PDO::FETCH_ASSOC     => true,
            PDO::ATTR_CASE       => PDO::CASE_NATURAL,
            PDO::ATTR_ERRMODE    => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_TIMEOUT    => env('DB_IFX_TIMEOUT', '15'),
        ],
    ],
    'informix-source-json' => [
        'driver'  => 'informix-json',
        'source'  => 'source',
        'uri'     => env('DB_IFX_URI', 'http://exmaple.org/json'),
        'token'   => env('DB_IFX_TOKEN', 'SDL3490FI2902309DSFK203SDKL2334202'),
    ],
];
