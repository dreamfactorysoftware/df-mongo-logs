<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'logsdb' => [
        'driver'   => 'mongodb',
        'host'     => env('LOGSDB_HOST', 'localhost'),
        'port'     => env('LOGSDB_PORT', 27017),
        'database' => env('LOGSDB_DATABASE'),
        'username' => env('LOGSDB_USERNAME'),
        'password' => env('LOGSDB_PASSWORD'),
        'options'  => [
            'database' => 'admin' // sets the authentication database required by mongo 3
        ]
    ],
];
