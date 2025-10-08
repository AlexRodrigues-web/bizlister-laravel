<?php

return [

    "defaults" => [
        "guard" => "web",
        "passwords" => "users",
    ],

    "guards" => [
        "web" => [
            "driver" => "session",
            "provider" => "users",
        ],

        "api" => [
            "driver" => "token",
            "provider" => "users",
            "hash" => false,
        ],
    ],

    "providers" => [
        "users" => [
            "driver" => "eloquent",
            "model" => App\Models\User::class,
        ],
        // Se você quiser usar o provider "database", comente o de cima e
        // descomente o bloco abaixo e ajuste a tabela:
        // "users" => [
        //     "driver" => "database",
        //     "table" => "laravel_users",
        // ],
    ],

    "passwords" => [
        "users" => [
            "provider" => "users",
            "table" => "password_resets",
            "expire" => 60,
            "throttle" => 60,
        ],
    ],

    "password_timeout" => 10800,

];