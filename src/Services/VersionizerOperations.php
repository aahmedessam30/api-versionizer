<?php

namespace Ahmedessam\ApiVersionizer\Services;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class VersionizerOperations extends BaseVersionizer
{
    protected function deleteDirectory($dir): bool
    {
        if (!File::exists($dir)) {
            return true;
        }

        File::deleteDirectory($dir);

        return true;
    }

    protected function generateVersionedFiles(string $version, string $file): void
    {
        $file = Str::singular($file);

        if ($file === 'route') {
            $this->generateRouteFiles($version, $file);
            return;
        }

        $folderPath = $this->buildFolderPath($file, $version);

        File::ensureDirectoryExists($folderPath);

        $this->createVersionFolders($version, $folderPath);
    }

    /**
     * Generate folders for versioned files.
     *
     * @param string $version
     * @param string $baseFolder
     * @return void
     */
    protected function createVersionFolders(string $version, string $baseFolder): void
    {
        $entries = $this->getVersionedApiFolders($version);

        collect($entries)->each(function ($entry, $key) use ($baseFolder) {
            $path = $baseFolder . DIRECTORY_SEPARATOR . ($entry ?? ucfirst($key));
            File::ensureDirectoryExists($path);
        });
    }

    /**
     * Build the folder path for the versioned files.
     *
     * @param string $file
     * @param string $version
     * @return string
     */
    protected function buildFolderPath(string $file, string $version): string
    {
        return $this->getFolderPath($file)
            . DIRECTORY_SEPARATOR
            . ucfirst($this->getDefaultDirectory())
            . DIRECTORY_SEPARATOR
            . ucfirst($version);
    }

    protected function generateRouteFiles($version, $name): void
    {
        foreach ($this->getVersionFiles($version) as $file) {
            if (!isset($file['name'])) {
                continue;
            }

            $folder = "routes" . DIRECTORY_SEPARATOR . $this->getDefaultDirectory(true) . DIRECTORY_SEPARATOR . $version;

            if (array_key_exists('group', $file)) {
                $folder .= DIRECTORY_SEPARATOR . strtolower($file['group']);
            }

            $this->generateFileIfNotExists($file['name'], $folder, $name);
        }
    }

    protected function generateFileIfNotExists($file, $path, $stubName): void
    {
        $filePath = base_path($path . DIRECTORY_SEPARATOR . "$file.php");

        if (File::exists($filePath)) {
            return;
        }

        File::ensureDirectoryExists(dirname($filePath));

        $stub = StubGenerator::getStub($stubName, __DIR__ . '/../stubs');

        StubGenerator::saveStub($filePath, $stub);
    }

    protected function generateStub($file, $path, $version): void
    {
        $stub = StubGenerator::getStub($file, __DIR__ . '/../stubs');

        if (StubGenerator::isDefaultStub($stub)) {
            $namespace = $this->getNamespace($path);
            $className = ucfirst($file);
            $stub      = StubGenerator::replaceStub($stub, ['{{ namespace }}', '{{ class }}'], [$namespace, $className]);
        } else {
            $stub = StubGenerator::replaceStub($stub, ['{{ version }}'], [ucfirst($version)]);
        }

        StubGenerator::saveStub($path . DIRECTORY_SEPARATOR . ucfirst($file) . ".php", $stub);
    }

    protected function getNamespace($path): string
    {
        return Str::of($path)
            ->replace(base_path(), '')
            ->after('App')
            ->when(fn($str) => $str->startsWith(DIRECTORY_SEPARATOR), fn($str) => $str->after(DIRECTORY_SEPARATOR))
            ->explode(DIRECTORY_SEPARATOR)
            ->map(fn($str) => ucfirst($str))
            ->join('\\');
    }

    protected function copyVersionFiles($version, $newVersion, $folder): void
    {
        File::ensureDirectoryExists($folder);

        $entries = array_diff(scandir($folder), ['.', '..']);

        if (empty($entries)) {
            $this->createNewDirectory($version, $newVersion, $folder);
            return;
        }

        foreach ($entries as $entry) {
            $source = $folder . DIRECTORY_SEPARATOR . $entry;
            $destination = $this->getDestinationPath($version, $newVersion, $folder) . DIRECTORY_SEPARATOR . $entry;

            if (is_dir($source)) {
                $this->copyVersionFiles($version, $newVersion, $source);
            } else {
                $this->copyAndUpdateFile($source, $destination, $version, $newVersion);
            }
        }
    }

    private function createNewDirectory($version, $newVersion, $folder): void
    {
        $destination = Str::of($folder)
            ->replace(ucfirst($version), ucfirst($newVersion))
            ->replace($version, $newVersion)
            ->value();

        File::ensureDirectoryExists($destination);
    }

    private function getDestinationPath($version, $newVersion, $folder): string
    {
        return Str::of($folder)
            ->replace(ucfirst($version), ucfirst($newVersion))
            ->replace($version, $newVersion)
            ->value();
    }

    private function copyAndUpdateFile($source, $destination, $version, $newVersion): void
    {
        File::ensureDirectoryExists(dirname($destination));
        File::copy($source, $destination);

        $content        = File::get($destination);
        $updatedContent = $this->updateNamespacesAndUses($content, $version, $newVersion);

        if ($content !== $updatedContent) {
            File::put($destination, $updatedContent);
        }
    }

    private function updateNamespacesAndUses($content, $version, $newVersion): string
    {
        $updatedContent = $content;

        if (preg_match('/namespace (.*);/', $content, $matches)) {
            $updatedContent = str_replace($matches[1], str_replace(ucfirst($version), ucfirst($newVersion), $matches[1]), $updatedContent);
        }

        if (preg_match_all('/use (.*);/', $content, $matches)) {
            foreach ($matches[1] as $use) {
                $updatedContent = str_replace($use, str_replace(ucfirst($version), ucfirst($newVersion), $use), $updatedContent);
            }
        }

        return $updatedContent;
    }

    protected function getPath($file, $version): string
    {
        return $this->getVersionedFolder($file, $version);
    }

    protected function getVersionPath($version): string
    {
        return base_path("routes" . DIRECTORY_SEPARATOR . $this->getDefaultDirectory(true) . DIRECTORY_SEPARATOR . $version);
    }

    protected function getVersionedFolder($file, $version): string
    {
        $file   = Str::singular($file);
        $folder = $this->getFolderPath($file) . DIRECTORY_SEPARATOR;

        return $file !== 'route'
            ? $folder . ucfirst($this->getDefaultDirectory()) . DIRECTORY_SEPARATOR . ucfirst($version)
            : $folder . $this->getDefaultDirectory(true) . DIRECTORY_SEPARATOR . $version;
    }
}
