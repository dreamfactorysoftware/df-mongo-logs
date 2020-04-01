<?php

namespace DreamFactory\Core\MongoLogs\Utility\HttpLogger;

use Carbon\Carbon;
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

        $bodyAsJson = json_encode($request->except(config('http-logger.except')));

        $files = array_map(function (UploadedFile $file) {
            return $file->getRealPath();
        }, iterator_to_array($request->files));

        $timestamp = Carbon::now();

        $message = "{$method} {$uri} - Body: {$bodyAsJson} - Files: ".implode(', ', $files);

        DB::connection('logsdb')->collection('access')->insert(
            [
                'timestamp' => $timestamp->toDateTimeString(),
                'method' => $method,
                'uri' => $uri,
                'body' => $bodyAsJson,
                'expireAt' => new UTCDateTime(Carbon::now()->addDays(45)->getTimestamp()*1000)
            ]
        );

        Log::info($message);
    }
}