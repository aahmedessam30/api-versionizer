<?php

namespace Ahmedessam\ApiVersionizer\Console\Commands;

use Illuminate\Console\Command;
use Ahmedessam\ApiVersionizer\Facades\ApiVersionizer;

class ApiVersionizerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected string $signature = 'api:versionize {--versions= : the versions to versionize the api}
    {--copy= : copy this version to another version} {--to= : the version to copy to}
    {--delete= : delete this version} {--force : force the operation}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected string $description = 'Versionize the api routes, controllers, and requests, etc.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        try {
            $versions = $this->getVersions($this->option('versions'));

            if ($this->option('copy')) {

                $this->components->info('Copying the api version...');

                if (!$this->option('to')) {
                    throw new \RuntimeException('Please provide the version to copy to.');
                }

                $this->copyVersion(
                    $this->getVersions($this->option('copy'))[0],
                    $this->getVersions($this->option('to'))[0]
                );

                return;
            }

            if ($this->option('delete')) {

                $this->components->info('Deleting the api version...');

                $version = $this->getVersions($this->option('delete'))[0];

                if ($this->components->confirm("Are you sure you want to delete the `$version` version, which will delete all the files in version `$version` Directories?")) {
                    $this->deleteVersion($version);
                }
                return;
            }

            $this->components->info('Versionizing the api...');

            ApiVersionizer::versionize($versions);

            $this->components->info('Api versionized successfully.');

        } catch (\Exception $e) {
            $this->components->error($e->getMessage());
        }
    }

    private function copyVersion($version, $newVersion): void
    {
        if (!$version || !$newVersion) {
            throw new \RuntimeException('Please provide the version and the new version to copy.');
        }

        ApiVersionizer::copy($version, $newVersion);

        $this->components->info('Api version copied successfully, update current_version in config/api-versionizer.php to the new version.');
    }

    private function deleteVersion($version): void
    {
        if (!$version) {
            throw new \RuntimeException('Please provide the version to delete.');
        }

        if (!$this->option('force')) {
            if ($version === ApiVersionizer::getDefaultVersion()) {
                throw new \RuntimeException('You can not delete the default version.');
            }

            if ($version === ApiVersionizer::getFallbackVersion()) {
                throw new \RuntimeException('You can not delete the fallback version.');
            }

            if (!in_array($version, ApiVersionizer::getVersionsFromRoutes(), true)) {
                throw new \RuntimeException('The version you are trying to delete does not exist.');
            }
        }

        $this->components->warn('Deleting the version will delete all the files in version Directories.');

        ApiVersionizer::delete($version);

        $this->components->info('Api version deleted successfully, update current_version in config/api-versionizer.php to the new version.');
    }

    private function getVersions($versions): array
    {
        if (!$versions) {
            return [ApiVersionizer::getDefaultVersion()];
        }

        if (str($versions)->contains(',')) {

            $versions = explode(',', $versions);

            return array_map(fn ($version) => $this->formatVersion($version), $versions);
        }

        return [$this->formatVersion($versions)];
    }

    private function formatVersion($version): string
    {
        $version = str($version)->trim()->lower()->value();

        if (!str_starts_with($version, 'v')) {
            $version = "v$version";
        }

        return $version;
    }
}
