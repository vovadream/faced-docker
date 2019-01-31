<?php

//webrtc_url - must be startet by https://
//camplay_url - must be startet by https://

return [
    'settings' => [
        'displayErrorDetails' => true,
        'path_to_core' => __DIR__."/../",
        'path_to_module' => __DIR__.'/src/module/',
        'path_to_core_uploads' => __DIR__ . '/../uploads/',
        'web_path' => '/',
        'web_path_to_uploads' => '/skudFace/public/uploads/',
        'webrtc_url' => 'https://webrtc.uwpw.ru/',
        'camplay_url' => 'https://uwpw.ru:9999/',
        'ff_server' => 'http://uwpw.ru:8000',
        'ff_token' => 'Authorization: Token VHc3-zYwp',
        'ff_photo_url' => '/uploads/image.jpg',
        'sertificate_pem' => '/cert/fp_main_server.crt',
        'sertificate_key' => '/cert/fp_main_server.key',

        //Настройки для деплоя
        'web-hook_token' => 'mwwlELyv5Q9ZHWFEa5',
        'git-branch' => 'master',

        'db' => [ // Настройки БД
            'driver' => 'pgsql',
            'host' => 'localhost',
            'dbname' => 'court',
            'user' => 'postgres',
            'pass' => 'nbjeqPG775ax'
        ],

        'sigur' => [ //Настройки для интеграции с Sigur
            'port' => 3312,
            'ip' => '192.168.56.101',
            'login' => 'Administrator',
            'password' => '123',
            'eq_id' => 11
        ],
    ],
];
