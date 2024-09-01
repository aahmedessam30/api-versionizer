<?php

namespace Ahmedessam\ApiVersionizer\Services;

use Illuminate\Support\Facades\File;
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

            $this->registerRouteServiceProvider();
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

        $this->registerRouteServiceProvider();
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

        $this->registerRouteServiceProvider();
    }

    private function registerRouteServiceProvider(): void
    {
        $appBootstrapPath = app()->bootstrapPath('providers.php');
        $content          = File::get($appBootstrapPath);

        if (!str_contains($content, 'Ahmedessam\ApiVersionizer\Providers\ApiVersionizerRouteServiceProvider::class')) {
            $content = str_replace(
                'AppServiceProvider::class',
                'AppServiceProvider::class,' . PHP_EOL . "\tAhmedessam\ApiVersionizer\Providers\ApiVersionizerRouteServiceProvider::class",
                $content
            );
            File::put($appBootstrapPath, $content);
        }
    }
}
