<?php

namespace Mecxer\WialonPackage\DependencyInjection;

use Mecxer\WialonPackage\WialonClient;
use Mecxer\WialonPackage\Commands\WialonCheckCommand; // <--- Import de la commande
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Extension\Extension;

class WialonExtension extends Extension
{
    public function load(array $configs, ContainerBuilder $container): void
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        // 1. Enregistrement du Client (Déjà fait)
        $clientDef = new Definition(WialonClient::class, [
            '$token' => $config['token'],
            '$baseUrl' => $config['base_url']
        ]);
        $clientDef->setPublic(true);
        $clientDef->setAutowired(true);
        $container->setDefinition(WialonClient::class, $clientDef);
        $container->setAlias('wialon', WialonClient::class);
       
        $commandDef = new Definition(WialonCheckCommand::class);
        $commandDef->setAutowired(true);      // Pour qu'il injecte WialonClient tout seul
        $commandDef->setAutoconfigured(true); // Pour qu'il lise l'attribut #[AsCommand]
        $commandDef->addTag('console.command'); // Pour dire à Symfony "C'est une commande !"
        
        $container->setDefinition(WialonCheckCommand::class, $commandDef);
    }
}