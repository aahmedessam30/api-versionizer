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
}
