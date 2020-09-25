<?php

namespace DreamFactory\Core\MongoLogs\Utility\HttpLogger;

use Carbon\Carbon;
use DreamFactory\Core\MongoLogs\Utility\AsyncLogger\AsyncLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use MongoDB\BSON\UTCDateTime;
use Spatie\HttpLogger\LogWriter;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MongoLogWriter implements LogWriter
{
    public function logRequest(Request $request)
    {
        $method = strtoupper($request->getMethod());

        $uri = $request->getPathInfo();

        if(substr($uri, -1) == '/') {
            $uri = substr($uri, 0, -1);
        }

        $files = array_map(function (UploadedFile $file) {
            return $file->getRealPath();
        }, iterator_to_array($request->files));

        $timestamp = Carbon::now();

        $message = "{$method} {$uri} - Files: ".implode(', ', $files);

        $record = [
            'timestamp' => $timestamp->toDateTimeString(),
            'date' => $timestamp->toDateString(),
            'method' => $method,
            'uri' => $uri,
            'expireAt' => new UTCDateTime(Carbon::now()->addDays(15)->getTimestamp()*1000),
            'apiKey' => $request->header('X-DreamFactory-API-Key'),
        ];

        if (env('LOGSDB_ASYNC') == 'true') {
            AsyncLogger::logRequest($record);
        } else {
            DB::connection('logsdb')->collection('access')->insert($record);
        }

        Log::info($message);
    }
}
