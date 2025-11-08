<?php

return [

    

    'defaults' => [
        'guard' => 'web',
        'passwords' => 'users',
    ],

    

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
    ],

    

    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model' => App\Models\Usuario::class,
        ],

        
        
        
        
    ],

    

    'passwords' => [
        'users' => [
            'provider' => 'users',
            'table' => 'tbl_ms_token_recuperacion',
            'expire' => 60,
            'throttle' => 60,
        ],
    ],

    

    'password_timeout' => 10800,

];
