<?php

return [

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'app_users',
        ],

        'api' => [
            'driver' => 'token',
            'provider' => 'app_users',
            'hash' => false,
        ],
    ],

    'providers' => [
        'app_users' => [
            'driver' => 'eloquent',
            'model' => App\Models\AppUser::class,
        ],
        // Se quiser manter o antigo, deixe comentado:
        // 'users' => [
        //     'driver' => 'eloquent',
        //     'model' => App\Models\User::class,
        // ],
    ],

    'passwords' => [
        'users' => [
            'provider' => 'app_users',
            'table' => 'password_resets',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    'password_timeout' => 10800,

];
