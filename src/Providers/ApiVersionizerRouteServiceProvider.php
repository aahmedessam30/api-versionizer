<?php

namespace Ahmedessam\ApiVersionizer\Providers;

use Carbon\Laravel\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Ahmedessam\ApiVersionizer\Facades\ApiVersionizer;
use Ahmedessam\ApiVersionizer\Exceptions\ApiVersionizerException;

class ApiVersionizerRouteServiceProvider extends ServiceProvider
{
    /**
     * @throws ApiVersionizerException
     */
    public function boot()
    {
        $this->mapApiVersionsRoutes();
    }

    /**
     * @throws ApiVersionizerException
     */
    protected function mapApiVersionsRoutes(): void
    {
        $reqVersion = ApiVersionizer::getVersionFromRequest();

        if (ApiVersionizer::getVersionFiles($reqVersion) && file_exists(base_path(ApiVersionizer::getRoutePath($reqVersion)))) {

            foreach (ApiVersionizer::getVersionFiles($reqVersion) as $version) {

                $route = Route::middleware(ApiVersionizer::getVersionMiddlewares($version))
                    ->namespace(ApiVersionizer::getVersionedControllersPath($version))
                    ->prefix(ApiVersionizer::getVersionedFilesPrefix($version))
                    ->as(ApiVersionizer::getVersionedAs($version));

                $route->group(base_path(ApiVersionizer::getRoutePath($reqVersion) . "/{$version['name']}.php"));
            }
        }
    }
}
