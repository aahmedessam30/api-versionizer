<?php

namespace Ahmedessam\ApiVersionizer\Middleware;

class LocalizationMiddleware
{
    public function handle($request, $next)
    {
        config([
            'app.locale'          => $request->header('Accept-Language', config('app.locale')),
            'app.fallback_locale' => $request->header('Accept-Fallback-Language', config('app.fallback_locale')),
            'app.timezone'        => $request->header('Accept-Timezone', config('app.timezone'))
        ]);

        return $next($request);
    }
}
