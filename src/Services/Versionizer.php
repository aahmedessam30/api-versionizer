<?php

namespace Ahmedessam\ApiVersionizer\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;

class Versionizer extends VersionizerOperations
{
    public function versionize($versions): void
    {
        foreach ($versions as $version) {
            $versionPath = $this->getVersionPath($version);

            File::ensureDirectoryExists($versionPath);

            foreach ($this->getVersionedFolders() as $file) {
                $this->generateVersionedFiles($version, $file);
            }
        }
    }

    public function copy($version, $newVersion): void
    {
        $versionPath = $this->getVersionPath($version);

        File::ensureDirectoryExists($versionPath);

        foreach ($this->getVersionedFolders() as $file) {
            $folder = $this->getVersionedFolder($file, $version);

            $this->copyVersionFiles($version, $newVersion, $folder);
        }
    }

    public function delete($version): void
    {
        $versionPath = $this->getVersionPath($version);

        if (File::isDirectory($versionPath)) {
            File::deleteDirectory($versionPath);
        }

        foreach ($this->getVersionedFolders() as $file) {
            $this->deleteDirectory($this->getPath($file, $version));
        }
    }

    public function registerMacros(): void
    {
        Route::macro('trash', function ($name, $controller) {
            Route::get("$name/trash", "$controller@trash")->name("$name.trash");
        });

        Route::macro('restore', function ($name, $controller, $param = 'id') {
            Route::patch("$name/{{$param}}/restore", "$controller@restore")->name("$name.restore");
        });

        Route::macro('forceDelete', function ($name, $controller, $param = 'id') {
            Route::delete("$name/{{$param}}/force-delete", "$controller@forceDelete")->name("$name.force-delete");
        });

        Route::macro('crud', function (
            string   $name,
            string   $controller,
            callable $callback = null,
            array    $except = [],
            array    $only = [],
            ?string  $param = null,
            ?array   $params = null,
        ) {
            $param           = $param ?? Str::singular($name);
            $defaultActions  = ['index', 'store', 'show', 'update', 'destroy'];
            $filteredActions = $only
                ? array_intersect($defaultActions, $only)
                : array_diff($defaultActions, $except);

            $resource = Route::apiResource($name, $controller)->only($filteredActions);

            if ($params) {
                $resource->parameters($params);
            }

            collect([
                ['macro' => 'trash'],
                ['macro' => 'restore', 'param' => $param],
                ['macro' => 'forceDelete', 'param' => $param],
            ])
                ->reject(fn($route) => in_array($route['macro'], $except, true))
                ->each(fn($route) => Route::{$route['macro']}($name, $controller, $route['param'] ?? null));

            if ($callback) {
                $callback();
            }
        });
    }
}
