<?php

namespace Ahmedessam\ApiVersionizer\Middleware;

use Ahmedessam\ApiVersionizer\Facades\ApiVersionizer;
use Ahmedessam\ApiVersionizer\Exceptions\ApiVersionizerException;

class ApiVersionizerMiddleware
{
    /**
     * @throws ApiVersionizerException
     */
    public function handle($request, $next)
    {
        $version = ApiVersionizer::getVersionFromRequest();

        $request->merge(['api_version' => $version]);

        $request->headers->set('Accept', "application/json");
        $request->headers->set('Content-Type', "application/json");
        $request->headers->set('Accept', "application/vnd.$version+json");
        $request->headers->set(config('api-versionizer.versioning_key.header', 'Accept-Version'), $version);

        return $next($request);
    }
}
