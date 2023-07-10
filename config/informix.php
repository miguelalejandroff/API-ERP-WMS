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

    'informix-source-json' => [
        'driver'  => 'informix-json',
        'source'  => 'source',
        'uri'     => env('DB_IFX_URI', 'http://exmaple.org/json'),
        'token'   => env('DB_IFX_TOKEN', 'SDL3490FI2902309DSFK203SDKL2334202'),
    ],
];
