<?php

namespace Mecxer\WialonPackage\Tests\Feature;

use Mecxer\WialonPackage\Tests\TestCase;
use Mecxer\WialonPackage\WialonClient;

class CommandTest extends TestCase
{
    /** @test */
    public function it_can_run_the_wialon_check_command()
    {
        // 1. On "Mock" (simule) le client Wialon pour ne pas faire de vrai appel API HTTP
        // Cela permet de tester sans internet et sans vrai token.
        $this->mock(WialonClient::class, function ($mock) {
            $mock->shouldReceive('setVerifyPath')->once();
            $mock->shouldReceive('login')->once(); // On s'attend à ce que login() soit appelé
            $mock->shouldReceive('getSessionId')->andReturn('fake_session_id');
            $mock->shouldReceive('getUnits')->andReturn([]);
        });

        $certDirectory = $this->app->storagePath('certif');
        if (!is_dir($certDirectory)) {
            mkdir($certDirectory, 0755, true);
        }
        $certPath = $certDirectory . DIRECTORY_SEPARATOR . 'cacert.pem';
        file_put_contents($certPath, 'dummy-cert');

        $this->artisan('wialon:check')
            ->assertSuccessful() // Vérifie que le code de retour est 0 (SUCCESS)
            ->expectsOutputToContain('Démarrage du test Wialon')
            ->expectsOutputToContain('Connexion réussie');

        $this->assertFileExists($certPath);
    }
}
