<?php
namespace DreamFactory\Core\MongoLogs;

use DreamFactory\Core\MongoLogs\Commands\MyCommand;
use DreamFactory\Core\MongoLogs\Utility\AsyncLogger\AsyncLogger;
use Illuminate\Routing\Router;

use Illuminate\Support\Facades\Route;
use Spatie\HttpLogger\Middlewares\HttpLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        if (env('LOGSDB_ENABLED') != 'true') {
            return;
        }

        if (env('LOGSDB_ASYNC') == 'true') {
            AsyncLogger::registerAsyncLogger($this->app);
        }

        $configPath = __DIR__ . '/../config/http-logger.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('http-logger.php');
        } else {
            $publishPath = base_path('config/http-logger.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');
        // add migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->addMiddleware();
    }

    public function register()
    {
        if (env('LOGSDB_ENABLED') != 'true') {
            return;
        }

        $this->mergeConfigFrom(__DIR__ . '/../config/logs-db.php', 'database.connections');
        $this->mergeConfigFrom(__DIR__ . '/../config/http-logger.php', 'http-logger');
    }

    /**
     * Register any middleware aliases.
     *
     * @return void
     */
    protected function addMiddleware()
    {
        Route::aliasMiddleware('df.http_logger', HttpLogger::class);
        Route::pushMiddlewareToGroup('df.api', 'df.http_logger');
    }
}
