<?php

namespace Mecxer\WialonPackage\Tests\Support;

use Orchestra\Testbench\Foundation\PackageManifest as TestbenchPackageManifest;

class PackageManifest extends TestbenchPackageManifest
{
    /**
     * Écrit le manifest sans opération de renommage (compatible environnements verrouillés).
     *
     * @param  array  $manifest
     * @return void
     *
     * @throws \Exception
     */
    protected function write(array $manifest)
    {
        if (! is_writable($dirname = dirname($this->manifestPath))) {
            throw new \Exception("The {$dirname} directory must be present and writable.");
        }

        $this->files->put(
            $this->manifestPath,
            '<?php return '.var_export($manifest, true).';'
        );
    }
}
