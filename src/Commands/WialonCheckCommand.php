<?php

namespace Mecxer\WialonPackage\Commands;

use Mecxer\WialonPackage\WialonClient;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'wialon:check',
    description: 'Teste la connexion API Wialon et gère le certificat local.'
)]
class WialonCheckCommand extends Command
{
    private WialonClient $client;

    public function __construct(WialonClient $client)
    {
        parent::__construct();
        $this->client = $client;
    }

    protected function configure(): void
    {
        $this->addOption('download', null, InputOption::VALUE_NONE, 'Télécharger le certificat de sécurité');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('🚀 Démarrage du test Wialon (Symfony Edition)');

        // 1. Gestion Certificat (On réutilise la logique PHP pure)
        // Note: storage_path() n'existe pas dans Symfony, on utilise une logique relative
        $certPath = $this->handleCertificate($io, $input->getOption('download'));

        if ($certPath) {
            $this->client->setVerifyPath($certPath);
        }

        // 2. Test Connexion
        $io->section("Tentative de connexion à l'API...");
        
        try {
            $this->client->login();
            $io->success("Connexion réussie ! Session ID: " . $this->client->getSessionId());

            $units = $this->client->getUnits();
            $io->text("Véhicules trouvés : " . count($units));

        } catch (\Exception $e) {
            $io->error("Erreur : " . $e->getMessage());
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }

    private function handleCertificate(SymfonyStyle $io, bool $download): ?string
    {
        // En Symfony, on stocke souvent dans var/ ou on reste dans le dossier du package pour le test
        // Pour faire simple et compatible, on va cibler un dossier temporaire ou relatif
        $directory = sys_get_temp_dir() . '/wialon-certs'; 
        $fullPath = $directory . '/cacert.pem';

        if (!file_exists($directory)) {
            mkdir($directory, 0777, true);
        }

        if ($download || !file_exists($fullPath)) {
            $io->text("Téléchargement du certificat...");
            try {
                copy('https://curl.se/ca/cacert.pem', $fullPath);
                $io->success("Certificat téléchargé : $fullPath");
            } catch (\Exception $e) {
                $io->error("Erreur téléchargement : " . $e->getMessage());
                return null;
            }
        }

        return file_exists($fullPath) ? $fullPath : null;
    }
}