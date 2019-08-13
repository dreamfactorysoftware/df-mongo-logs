<?php
namespace DreamFactory\Core\Skeleton;

use Illuminate\Routing\Router;

use Illuminate\Support\Facades\Route;
use Spatie\HttpLogger\Middlewares\HttpLogger;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{
    public function boot()
    {
        // add our database config
        $configPath = __DIR__ . '/../config/database.php';
        if (function_exists('config_path')) {
            $publishPath = config_path('database.php');
        } else {
            $publishPath = base_path('config/database.php');
        }
        $this->publishes([$configPath => $publishPath], 'config');

        // add migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        $this->addMiddleware();
    }

    /**
     * Register any middleware aliases.
     *
     * @return void
     */
    protected function addMiddleware()
    {
        // the method name was changed in Laravel 5.4
        if (method_exists(Router::class, 'aliasMiddleware')) {
            Route::aliasMiddleware('df.http_logger', HttpLogger::class);
        } else {
            /** @noinspection PhpUndefinedMethodInspection */
            Route::middleware('df.http_logger', HttpLogger::class);
        }

        Route::pushMiddlewareToGroup('df.api', 'df.http_logger');
    }
}
