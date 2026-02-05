<?php

namespace Mecxer\WialonPackage\DependencyInjection;

use Mecxer\WialonPackage\WialonClient;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class WialonExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        // 1. Charger la configuration définie dans Configuration.php
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // 2. Créer la définition du service WialonClient
        $definition = new Definition(WialonClient::class, [
            '$token' => $config['token'],
            '$base_url' => $config['base_url']
        ]);

        // 3. Rendre le service public et autowirable
        $definition->setPublic(true);
        $definition->setAutowired(true);

        // 4. Enregistrer le service dans Symfony
        $container->setDefinition(WialonClient::class, $definition);

        // Créer un alias "wialon" (optionnel mais pratique)
        $container->setAlias('wialon', WialonClient::class);
    }
}
