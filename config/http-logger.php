<?php

use Utility\HttpLogger\AllMethodsLogger;
use Utility\HttpLogger\MongoLogWriter;

return [

    /*
     * The log profile which determines whether a request should be logged.
     * It should implement `LogProfile`.
     */
    'log_profile' => AllMethodsLogger::class,

    /*
     * The log writer used to write the request to a log.
     * It should implement `LogWriter`.
     */
    'log_writer' => MongoLogWriter::class,

    /*
     * Filter out body fields which will never be logged.
     */
    'except' => [
        'password',
        'password_confirmation',
    ],

];
