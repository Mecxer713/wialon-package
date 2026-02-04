<?php

namespace Mecxer\WialonPackage\Commands;

use Illuminate\Console\Command;
use Mecxer\WialonPackage\Facades\Wialon; // On importe la Façade

class TestWialonConnection extends Command
{
    protected $signature = 'wialon:check {--download : Télécharger le certificat de sécurité}';

    protected $description = 'Teste la connexion API Wialon et gère le certificat local';

    /**
     * Exécution de la commande.
     * Note : On n'injecte plus WialonClient ici, on utilise la Façade statique.
     */
    public function handle()
    {
        $this->info("🚀 Démarrage du test Wialon (Mode Façade)...");

        // 1. Gestion du Certificat
        $certPath = $this->handleCertificate();

        // 2. Configuration du certificat via la Façade
        if ($certPath) {
            Wialon::setVerifyPath($certPath);
        }

        // 3. Test de Connexion
        $this->checkConnection();

        return Command::SUCCESS;
    }

    protected function checkConnection()
    {
        $this->comment("Tentative de connexion à l'API...");

        try {
            // --- Étape A : Login via Façade ---
            Wialon::login();
            
            $this->info("✅ Connexion réussie !");
            // Accès au Session ID via Façade
            $this->line("Session ID (EID) : " . Wialon::getSessionId());

            // --- Étape B : Récupération des véhicules via Façade ---
            $this->comment("🔍 Recherche des véhicules...");
        
            try {
                $units = Wialon::getUnits();
                $count = count($units);
                
                $this->info("Found $count units:");
                
                foreach (array_slice($units, 0, 5) as $unit) {
                    $id = $unit['id'] ?? '?';
                    $name = $unit['nm'] ?? 'Inconnu';
                    $this->line(" - [ID: $id] $name");
                }
                
                if ($count > 5) {
                    $this->line(" - ... et " . ($count - 5) . " autres.");
                }
                
            } catch (\Exception $e) {
                $this->error("⚠️ Erreur lors de la récupération des unités : " . $e->getMessage());
            }

        } catch (\Exception $e) {
            $this->error("❌ Échec critique de la connexion.");
            $this->error("Erreur : " . $e->getMessage());
            
            if (str_contains($e->getMessage(), 'certificate')) {
                $this->warn("Conseil : Essayez de lancer 'php artisan wialon:check --download'");
            }
        }
    }

    protected function handleCertificate()
    {
        // On cible le dossier storage/certif à la racine du storage
        // Utilisation de $this->laravel->storagePath() pour être compatible partout
        $directory = $this->laravel->storagePath('certif');
        $fileName = 'cacert.pem';
        $fullPath = $directory . DIRECTORY_SEPARATOR . $fileName;
        
        // Création du dossier s'il n'existe pas
        if (!file_exists($directory)) {
            if (!mkdir($directory, 0755, true) && !is_dir($directory)) {
                $this->error("❌ Impossible de créer le dossier : $directory");
                return null;
            }
        }

        // Téléchargement si demandé OU si le fichier est absent
        if ($this->option('download') || !file_exists($fullPath)) {
            $this->comment("Téléchargement du certificat officiel (curl.se)...");
            
            try {
                $content = file_get_contents('https://curl.se/ca/cacert.pem');
                
                if ($content === false || strlen($content) < 1000) {
                    throw new \Exception("Le téléchargement a échoué ou le fichier est vide.");
                }

                file_put_contents($fullPath, $content);
                $this->info("✅ Certificat sauvegardé : $fullPath");
                
            } catch (\Exception $e) {
                $this->error("❌ Erreur téléchargement : " . $e->getMessage());
                return null;
            }
        } else {
            $this->line("ℹ️ Utilisation du certificat existant : $fullPath");
        }

        return file_exists($fullPath) ? $fullPath : null;
    }
}