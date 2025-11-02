<?php

return [
    'proxies' => [
        '172.0.0.0/8',
        '10.0.0.0/8', 
        '192.168.0.0/16',
    ],
    
    'headers' => [
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT,
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO,
    ],
];