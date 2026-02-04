<?php

namespace Mecxer\WialonPackage\Tests;

use Orchestra\Testbench\TestCase as Orchestra;
use Mecxer\WialonPackage\WialonServiceProvider;
use Illuminate\Foundation\PackageManifest as IlluminatePackageManifest;
use Mecxer\WialonPackage\Tests\Support\PackageManifest as NonRenamingPackageManifest;

class TestCase extends Orchestra
{
    public static function setUpBeforeClass(): void
    {
        static::prepareTestbenchBasePath();

        parent::setUpBeforeClass();
    }

    protected function resolveApplicationResolvingCallback($app): void
    {
        parent::resolveApplicationResolvingCallback($app);

        $base = $app->make(IlluminatePackageManifest::class);

        $app->instance(
            IlluminatePackageManifest::class,
            new NonRenamingPackageManifest($base->files, $base->basePath, $base->manifestPath, $this)
        );
    }

    protected function resolveApplicationConfiguration($app)
    {
        parent::resolveApplicationConfiguration($app);

        $providers = $app['config']->get('app.providers', []);
        $manifest = [
            'providers' => $providers,
            'eager' => $providers,
            'deferred' => [],
        ];

        $manifestPath = $app->getCachedServicesPath();
        $manifestDir = dirname($manifestPath);
        if (!is_dir($manifestDir)) {
            mkdir($manifestDir, 0755, true);
        }

        file_put_contents($manifestPath, '<?php return '.var_export($manifest, true).';');
    }

    private static function prepareTestbenchBasePath(): void
    {
        $root = realpath(__DIR__ . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR . '..');
        if ($root === false) {
            $root = getcwd() ?: __DIR__;
        }

        $basePath = $root . DIRECTORY_SEPARATOR . '.testbench';
        $source = $root . DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR . 'orchestra' . DIRECTORY_SEPARATOR . 'testbench-core' . DIRECTORY_SEPARATOR . 'laravel';

        if (!is_dir($source)) {
            throw new \RuntimeException("Testbench skeleton introuvable : {$source}");
        }

        if (!is_dir($basePath)) {
            mkdir($basePath, 0755, true);
        }

        $bootstrapApp = $basePath . DIRECTORY_SEPARATOR . 'bootstrap' . DIRECTORY_SEPARATOR . 'app.php';
        if (!file_exists($bootstrapApp)) {
            static::copyDirectory($source, $basePath);
        }

        $_ENV['TESTBENCH_APP_BASE_PATH'] = $basePath;
        $_ENV['APP_BASE_PATH'] = $basePath;
    }

    private static function copyDirectory(string $source, string $destination): void
    {
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($source, \FilesystemIterator::SKIP_DOTS),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $item) {
            $target = $destination . DIRECTORY_SEPARATOR . $iterator->getSubPathName();

            if ($item->isDir()) {
                if (!is_dir($target)) {
                    mkdir($target, 0755, true);
                }
            } else {
                if (!copy($item->getPathname(), $target)) {
                    throw new \RuntimeException("Impossible de copier le fichier : {$target}");
                }
            }
        }
    }

    /**
     * Cette méthode dit à Testbench : "Charge mon ServiceProvider comme si on était dans Laravel"
     */
    protected function getPackageProviders($app)
    {
        return [
            WialonServiceProvider::class,
        ];
    }

    /**
     * Cette méthode dit à Testbench : "Charge ma Façade"
     */
    protected function getPackageAliases($app)
    {
        return [
            'Wialon' => \Mecxer\WialonPackage\Facades\Wialon::class,
        ];
    }
    
    /**
     * Configuration de l'environnement de test (Optionnel)
     * Ici on peut définir des variables d'environnement fake pour le test.
     */
    protected function defineEnvironment($app)
    {
        // On simule un token pour éviter que les tests ne plantent s'il manque
        $app['config']->set('wialon.token', 'test_token_fictif');
        $app['config']->set('wialon.base_url', 'https://hst-api.wialon.com/wialon/ajax.html');
    }
}
