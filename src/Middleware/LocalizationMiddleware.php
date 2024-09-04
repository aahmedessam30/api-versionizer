<?php

namespace Ahmedessam\ApiVersionizer\Middleware;

class LocalizationMiddleware
{
    public function handle($request, $next)
    {
        config([
            'app.locale'          => $request->header('Language', config('app.locale')),
            'app.fallback_locale' => $request->header('Fallback-Language', config('app.fallback_locale')),
            'app.timezone'        => $request->header('Accept-Timezone', config('app.timezone'))
        ]);

        return $next($request);
    }
}
