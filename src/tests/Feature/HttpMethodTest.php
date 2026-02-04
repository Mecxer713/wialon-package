<?php

namespace Mecxer\WialonPackage\Tests\Feature;

use Mecxer\WialonPackage\Tests\TestCase;
use Mecxer\WialonPackage\WialonClient;

class HttpMethodTest extends TestCase
{
    /** @test */
    public function it_uses_default_method_when_not_provided()
    {
        $client = new FakeWialonClient('token', 'https://example.test', [], 'POST');

        $client->call('core/search_items', ['x' => 1]);

        $this->assertSame('POST', $client->lastMethod);
    }

    /** @test */
    public function it_supports_call_post_and_call_get_helpers()
    {
        $client = new FakeWialonClient('token', 'https://example.test', [], 'POST');

        $client->callPost('core/search_items', ['x' => 1]);
        $this->assertSame('POST', $client->lastMethod);

        $client->callGet('core/get_statistics', ['x' => 1]);
        $this->assertSame('GET', $client->lastMethod);
    }
}

class FakeWialonClient extends WialonClient
{
    public ?string $lastMethod = null;

    protected function initClient($verify = null): void
    {
        // Pas d'HTTP réel en test.
    }

    public function login(): self
    {
        $this->sessionId = 'fake';
        return $this;
    }

    protected function request(string $svc, array $params, string $method = 'POST'): array
    {
        $this->lastMethod = strtoupper($method);
        return [];
    }
}
