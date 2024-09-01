<?php

namespace Ahmedessam\ApiVersionizer\Services;

use Ahmedessam\ApiVersionizer\Exceptions\ApiVersionizerException;
use Illuminate\Support\Facades\File;

class BaseVersionizer
{
    protected array $versionedFolders = ['routes', 'controllers', 'requests', 'resources'];

    /**
     * Get the versions.
     */
    public function getVersions(): array
    {
        return config('api-versionizer.versions', []);
    }

    /**
     * Get versions from route files
     * @return array
     */
    public function getVersionsFromRoutes(): array
    {
        $folders = File::directories(base_path('routes' . DIRECTORY_SEPARATOR . $this->getDefaultDirectory(true)));

        return collect($folders)->map(fn ($folder) => basename($folder))->toArray();
    }

    /**
     * Get the versioning strategy.
     */
    public function getVersioningStrategy(): string
    {
        return config('api-versionizer.strategy', 'uri');
    }

    /**
     * Get the versioning key.
     */
    public function getVersioningKey(): array
    {
        return config('api-versionizer.versioning_key');
    }

    /**
     * Get the version prefix.
     */
    public function getVersionPrefix(): string
    {
        return config('api-versionizer.prefix', 'v');
    }

    /**
     * Get fallback version.
     */
    public function getFallbackVersion(): string
    {
        return config('api-versionizer.fallback_version', config('api-versionizer.default_version', 'v1'));
    }

    /**
     * Get the default version.
     */
    public function getDefaultVersion(): string
    {
        return config('api-versionizer.current_version', config('api-versionizer.fallback_version', config('api-versionizer.default_version', 'v1')));
    }

    /**
     * Get the version info.
     */
    public function getVersionInfo(string $version): array
    {
        return config("api-versionizer.versions.$version", []);
    }

    /**
     * Get the active versions.
     */
    public function getActiveVersions(): array
    {
        return collect(config('api-versionizer.versions'))->filter(fn ($version) => $version['status'] === 'active')->keys()->toArray();
    }

    /**
     * Get default middlewares.
     */
    public function getDefaultMiddlewares(): array
    {
        return array_merge(config('api-versionizer.middlewares', []), ['api-versionizer', 'deprecated-version']);
    }

    /**
     * Get default files.
     */
    public function getDefaultFiles(): array
    {
        return config('api-versionizer.default_files', []);
    }

    /**
     * Get version files.
     */
    public function getVersionFiles(string $version): array
    {
        return array_key_exists($version, $this->getVersionInfo($version)) && array_key_exists('files', $this->getVersionInfo($version))
            ? $this->getVersionInfo($version)['files']
            : $this->getDefaultFiles();
    }

    /**
     * Get version middlewares.
     */
    public function getVersionMiddlewares($version): array
    {
        return array_key_exists('middlewares', $version) && is_array($version['middlewares']) && !empty($version['middlewares'])
            ? array_merge($this->getDefaultMiddlewares(), $version['middlewares'])
            : $this->getDefaultMiddlewares();
    }

    /**
     * Get default_directory.
     */
    public function getDefaultDirectory($lower = false): string
    {
        $directory = config('api-versionizer.default_directory');

        return $lower ? strtolower($directory) : $directory;
    }

    /**
     * Get the versioned folders.
     */
    public function getVersionedFolders(): array
    {
        return config('api-versionizer.versioned_folders', $this->versionedFolders);
    }

    /**
     * Get the versioned controllers path.
     */
    public function getVersionedControllersPath($version): string
    {
        $namespace = 'Http/Controllers/' . $this->getDefaultDirectory();

        if (array_key_exists('namespace', $version)) {
            $namespace .= "/{$version['namespace']}";
        }

        return $namespace;
    }

    protected function getFolderPath($name): string
    {
        $folderName = str($name)->plural()->camel()->title()->value();

        $path = match ($name) {
            'controller' => app_path("Http" . DIRECTORY_SEPARATOR . "Controllers"),
            'request'    => app_path("Http" . DIRECTORY_SEPARATOR . "Requests"),
            'resource'   => app_path("Http" . DIRECTORY_SEPARATOR . "Resources"),
            'model'      => app_path("Models"),
            'policy'     => app_path("Policies"),
            'rule'       => app_path("Rules"),
            'observer'   => app_path("Observers"),
            'middleware' => app_path("Http" . DIRECTORY_SEPARATOR . "Middleware"),
            'provider'   => app_path("Providers"),
            'service'    => app_path("Services"),
            'trait'      => app_path("Traits"),
            'test'       => base_path("tests"),
            'route'      => base_path("routes"),
            default      => $this->autoDiscoverFolderPath($folderName, default: app_path($folderName)),
        };

        return str($path)
            ->replace('/', DIRECTORY_SEPARATOR)
            ->when(fn($str) => !$str->startsWith(base_path()), fn($str) => base_path($str));
    }

    /**
     * Get route path.
     */
    public function getRoutePath($version): string
    {
        return 'routes' . DIRECTORY_SEPARATOR . $this->getDefaultDirectory(true) . DIRECTORY_SEPARATOR . $version;
    }

    /**
     * Get the versioned files prefix.
     * @throws ApiVersionizerException
     */
    public function getVersionedFilesPrefix($version): string
    {
        $prefix = $this->getDefaultDirectory(true);

        if ($this->getVersioningStrategy() === 'uri') {
            $prefix .= "/" . $this->getVersionFromRequest();
        }

        if (array_key_exists('prefix', $version)) {
            $prefix .= "/{$version['prefix']}";
        }

        return $prefix;
    }

    /**
     * Get the versioned as.
     * @throws ApiVersionizerException
     */
    public function getVersionedAs($version): string
    {
        $as = $this->getDefaultDirectory(true) . '.' . $this->getVersionFromRequest();

        if (array_key_exists('as', $version)) {
            $as .= ".{$version['as']}";
        }

        return "$as.";
    }

    /**
     * Get the version from the request.
     *
     * @throws ApiVersionizerException
     */
    public function getVersionFromRequest(): string
    {
        $request        = request();
        $defaultVersion = $this->getDefaultVersion();
        $version        = match(config('api-versionizer.strategy')) {
            'uri'    => $request->segment(2) ?: $defaultVersion,
            'header' => $request->header(config('api-versionizer.versioning_key.header'), $defaultVersion),
            'query'  => $request->query(config('api-versionizer.versioning_key.query'), $defaultVersion),
            default  => $defaultVersion,
        };

        $version = str($version)->trim()
            ->when(fn($str) => $str->startsWith('v'), fn ($str) => $str->replace('v', ''))
            ->when(fn($str) => !$str->startsWith($this->getVersionPrefix()), fn ($str) => $str->prepend($this->getVersionPrefix()))
            ->lower()
            ->value();

        if ($this->isApi() && $request->wantsJson()) {
            return $this->validateVersion($version);
        }

        return $version;
    }

    /**
     * Validate version.
     * @throws ApiVersionizerException
     */
    public function validateVersion($version): string
    {
        if (!array_key_exists($version, $this->getVersions())) {
            throw new ApiVersionizerException("API version $version is not found", 404);
        }

        if (!array_key_exists($version, $this->getActiveVersions())) {
            throw new ApiVersionizerException("API version $version is not active", 404);
        }

        return $version;
    }

    public function autoDiscoverFolderPath(string $folderName, string $basePath = null, string $default = null): ?string
    {
        $basePath = $basePath ?? base_path();

        $directories = File::directories($basePath);

        $directories = array_diff($directories, [base_path('vendor')]);

        foreach ($directories as $directory) {
            if (basename($directory) === $folderName) {
                return str($directory)->replace(base_path(), '')->value();
            }

            $subDirectory = $this->autoDiscoverFolderPath($folderName, $directory);

            if ($subDirectory) {
                return str($subDirectory)->replace(base_path(), '')->value();
            }
        }

        return $default;
    }

    /**
     * Get Deprecated Version.
     */
    public function getDeprecatedVersions(): array
    {
        return collect(config('api-versionizer.versions'))->filter(fn ($version) => $version['status'] === 'deprecated')->keys()->toArray() ?? [];
    }

    /**
     * Is api path.
     */
    public function isApi(): bool
    {
        return request()->is('api/*');
    }

    /**
     * Is from cli.
     */
    public function isCli(): bool
    {
        return app()->runningInConsole();
    }
}
