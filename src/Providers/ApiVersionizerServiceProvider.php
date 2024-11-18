<?php

namespace Ahmedessam\ApiVersionizer\Providers;

use Illuminate\Support\ServiceProvider;
use Ahmedessam\ApiVersionizer\Services\Versionizer;
use Ahmedessam\ApiVersionizer\Console\Commands\ApiVersionizerCommand;
use Ahmedessam\ApiVersionizer\Middleware\{
    ApiVersionizerMiddleware,
    DeprecatedVersionMiddleware,
    LocalizationMiddleware
};

class ApiVersionizerServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind('api-versionizer', function () {
            return new Versionizer();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                ApiVersionizerCommand::class
            ]);
        }

        $this->app['router']->aliasMiddleware('api-versionizer', ApiVersionizerMiddleware::class);
        $this->app['router']->aliasMiddleware('deprecated-version', DeprecatedVersionMiddleware::class);
        $this->app['router']->aliasMiddleware('localization', LocalizationMiddleware::class);

        $this->publishes([
            __DIR__ . '/../config/api-versionizer.php' => config_path('api-versionizer.php'),
        ], 'apiversionizer-config');

        $this->publishes([
            __DIR__ . '/../Middleware/ApiVersionizerMiddleware.php' => app_path('Http/Middleware/ApiVersionizerMiddleware.php'),
            __DIR__ . '/../Middleware/LocalizationMiddleware.php' => app_path('Http/Middleware/LocalizationMiddleware.php'),
        ], 'apiversionizer-middleware');

        $this->publishes([
            __DIR__ . '/ApiVersionizerRouteServiceProvider.php' => app_path('Providers/ApiVersionizerRouteServiceProvider.php'),
        ], 'apiversionizer-provider');
    }
}
