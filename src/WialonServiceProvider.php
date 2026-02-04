<?php

namespace Mecxer\WialonPackage;

use Illuminate\Support\ServiceProvider;

class WialonServiceProvider extends ServiceProvider
{
    /**
     * Méthode appelée au démarrage de Laravel.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/wialon.php' => $this->app->configPath('wialon.php'),
            ], 'wialon-config');

            $this->commands([
                \Mecxer\WialonPackage\Commands\TestWialonConnection::class,
            ]);
        }
    }

    /**
     * Méthode appelée lors de l'enregistrement du service.
     */
    public function register()
    {
        // 1. Fusionner la config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/wialon.php', 'wialon'
        );

        // 2. Enregistrer le client en Singleton
        $this->app->singleton(WialonClient::class, function ($app) {
            return new WialonClient(
                $app['config']->get('wialon.token'),
                $app['config']->get('wialon.base_url'),
                $app['config']->get('wialon.guzzle', [])
            );
        });

        // 3. Créer l'alias pour la Façade ('wialon')
        $this->app->alias(WialonClient::class, 'wialon');
    }
}
