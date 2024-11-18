<?php

namespace Ahmedessam\ApiVersionizer\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static getVersionFromRequest()
 * @method static getActiveVersions()
 * @method static getVersionInfo(string $version)
 * @method static getVersionFiles($version)
 * @method static getVersionedControllersPath($version)
 * @method static getVersionMiddlewares($version)
 * @method static getRoutePath($reqVersion)
 * @method static getVersionedFilesPrefix(mixed $version)
 * @method static getVersionedAs(mixed $version)
 * @method static versionize(string[] $versions)
 * @method static copyVersion(array|bool|string $version)
 * @method static getLatestVersion()
 * @method static getDeprecatedVersions()
 * @method static getDefaultVersion()
 * @method static copy(array|bool|string|null $option, array|string[] $versions)
 * @method static delete(array|bool|string|null $option)
 * @method static getFallbackVersion()
 * @method static getVersionsFromRoutes()
 * @method static useDefaultNamespace(mixed $version)
 */
class ApiVersionizer extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return 'api-versionizer';
    }
}
