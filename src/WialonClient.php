<?php

namespace Mecxer\WialonPackage;

use GuzzleHttp\Client as GuzzleClient;
use GuzzleHttp\Exception\GuzzleException;

class WialonClient
{
    protected GuzzleClient $httpClient;
    protected string $token;
    protected string $baseUrl;
    protected array $guzzleOptions = [];
    protected ?string $sessionId = null;

    /**
     * Constructeur.
     */
    public function __construct(
        string $token,
        string $baseUrl = 'https://hst-api.wialon.com/wialon/ajax.html',
        array $guzzleOptions = []
    )
    {
        $this->token = $token;
        $this->baseUrl = $baseUrl;
        $this->guzzleOptions = $guzzleOptions;

        // Initialisation du client (tente de détecter le certif automatiquement)
        $this->initClient();
    }

    /**
     * Initialise ou réinitialise le client Guzzle avec la gestion SSL.
     * @param string|bool|null $verify Chemin vers le certificat ou true/false
     */
    protected function initClient($verify = null): void
    {
        $options = array_merge([
            'base_uri' => $this->baseUrl,
            'timeout'  => 30,
        ], $this->guzzleOptions);

        if ($verify === null) {
            $verify = $options['verify'] ?? true;
        }

        // Si aucun chemin spécifique n'est donné, on regarde si un certificat existe
        // dans le dossier standard du package : storage/certif/cacert.pem
        if ($verify === true && function_exists('storage_path')) {
            $localCert = storage_path('certif/cacert.pem');
            if (file_exists($localCert)) {
                $verify = $localCert;
            }
        }

        $options['verify'] = $verify; // Utilise le fichier pem si trouvé, sinon le système par défaut
        $options['base_uri'] = $this->baseUrl;

        $this->httpClient = new GuzzleClient($options);
    }

    /**
     * Permet de forcer le chemin du certificat SSL (utilisé par la commande artisan).
     */
    public function setVerifyPath(string $path): void
    {
        $this->initClient($path);
    }

    /**
     * Authentification et récupération du Session ID (eid).
     */
    public function login(): self
    {
        if ($this->sessionId) {
            return $this;
        }

        $response = $this->request('token/login', [
            'token' => $this->token
        ]);

        if (isset($response['eid'])) {
            $this->sessionId = $response['eid'];
        } else {
            throw new \Exception('Wialon Login Failed: ' . json_encode($response));
        }

        return $this;
    }

    /**
     * Appel générique à l'API Wialon.
     */
    public function call(string $svc, array $params = []): array
    {
        if (!$this->sessionId && $svc !== 'token/login') {
            $this->login();
        }

        return $this->request($svc, $params);
    }

    /**
     * Wrapper interne Guzzle.
     */
    protected function request(string $svc, array $params): array
    {
        try {
            $encodedParams = json_encode($params, JSON_THROW_ON_ERROR);
        } catch (\JsonException $e) {
            throw new \RuntimeException('Paramètres JSON invalides pour Wialon.', 0, $e);
        }

        $queryParams = [
            'svc' => $svc,
            'params' => $encodedParams,
        ];

        if ($this->sessionId) {
            $queryParams['sid'] = $this->sessionId;
        }

        try {
            $response = $this->httpClient->get('', ['query' => $queryParams]);
            $rawBody = (string) $response->getBody();

            try {
                $data = json_decode($rawBody, true, 512, JSON_THROW_ON_ERROR);
            } catch (\JsonException $e) {
                throw new \RuntimeException("Réponse JSON invalide depuis Wialon (svc={$svc}).", 0, $e);
            }

            if (isset($data['error'])) {
                throw new \RuntimeException("Erreur API Wialon (svc={$svc}, code={$data['error']}).");
            }

            return $data;
        } catch (GuzzleException $e) {
            throw new \RuntimeException("HTTP Error: " . $e->getMessage(), 0, $e);
        }
    }

    public function getSessionId(): ?string
    {
        return $this->sessionId;
    }

    /**
     * Récupère la liste des unités (Véhicules).
     */
    public function getUnits(int $flags = 1): array
    {
        $response = $this->call('core/search_items', [
            'spec' => [
                'itemsType' => 'avl_unit',
                'propName'  => 'sys_name',
                'propValueMask' => '*',
                'sortType'  => 'sys_name'
            ],
            'force' => 1,
            'flags' => $flags,
            'from'  => 0,
            'to'    => 0
        ]);

        return $response['items'] ?? [];
    }
}
