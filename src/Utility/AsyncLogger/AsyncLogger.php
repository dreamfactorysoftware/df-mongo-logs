<?php

namespace DreamFactory\Core\MongoLogs\Utility\AsyncLogger;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AsyncLogger
{
    protected static $FILE = 'async-request.log';

    /**
     * @param Application $app
     */
    static function registerAsyncLogger($app) {
        $app->booted(function () use ($app)  {
            $schedule = $app->make(Schedule::class);
            $schedule->call(function () {
                Log::info('AsyncLogger::registerAsyncLogger - Check logs for saving');
                if (file_exists(storage_path(AsyncLogger::$FILE))) {
                    Log::info('AsyncLogger::registerAsyncLogger - Logs found. Saving logs to database');
                    /** @var string $content */
                    $content = file_get_contents(storage_path(AsyncLogger::$FILE));
                    unlink(storage_path(AsyncLogger::$FILE));
                    /** @var array $requests */
                    $requests = explode("\n", $content);
                    $arr = [];
                    foreach ($requests as $request) {
                        if (strlen($request) != 0) {
                            $record = unserialize($request);
                            $arr[] = $record;
                        }
                    }
                    DB::connection('logsdb')->collection('access')->insert($arr);
                    Log::info('AsyncLogger::registerAsyncLogger - Logs saved successful. Saved records volume: ' . count($arr));
                }
            })->everyMinute();
        });
    }

    /**
     * @param mixed $request
     */
    static function logRequest($request) {
        file_put_contents(storage_path(AsyncLogger::$FILE), serialize($request) . "\n", FILE_APPEND);
    }
}
