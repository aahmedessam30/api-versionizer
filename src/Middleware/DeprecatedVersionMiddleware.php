<?php

namespace Ahmedessam\ApiVersionizer\Middleware;

use Ahmedessam\ApiVersionizer\Facades\ApiVersionizer;

class DeprecatedVersionMiddleware
{
    public function handle($request, $next)
    {
        $response = $next($request);

        $deprecatedVersions = ApiVersionizer::getDeprecatedVersions();

        $version = ApiVersionizer::getVersionFromRequest();

        if (in_array($version, $deprecatedVersions, true)) {
            $response->headers->set('X-Deprecated-Version', $version);
            $response->headers->set('X-Deprecated-Message', $this->getDeprecatedWarning($version, $deprecatedVersions));
            $response->headers->set('X-Deprecated-At', $deprecatedVersions[$version]['deprecated_at']);

            if (!in_array($deprecatedVersions[$version]['deprecated_at'], [null, ''], true) && strtotime($deprecatedVersions[$version]['deprecated_at']) < time()) {
                return response()->json(['message' => 'This version is deprecated and no longer supported.'], 410);
            }
        }

        return $response;
    }

    private function getDeprecatedWarning($version, $deprecatedVersions)
    {
        if ($deprecatedVersions[$version]['message']) {
            return $deprecatedVersions[$version]['message'];
        }

        $message = 'This version is deprecated. Please upgrade to the latest version.';

        if (!in_array($deprecatedVersions[$version]['deprecated_at'], [null, ''], true) && strtotime($deprecatedVersions[$version]['deprecated_at']) > time()) {
            $message .= ' This version will be removed on ' . $deprecatedVersions[$version]['deprecated_at'];
        }

        return $message;
    }
}
