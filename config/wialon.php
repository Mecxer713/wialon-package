<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Wialon API Token
    |--------------------------------------------------------------------------
    |
    | Ton token d'accès Wialon. Tu peux le générer via l'interface Wialon
    | ou via une requête d'authentification initiale.
    |
    */
    'token' => env('WIALON_TOKEN', ''),

    /*
    |--------------------------------------------------------------------------
    | Wialon Base URL
    |--------------------------------------------------------------------------
    |
    | L'URL de base de l'API Wialon. Elle peut changer selon le serveur
    | d'hébergement (hosting.wialon.com, etc.).
    |
    */
    'base_url' => env('WIALON_BASE_URL', 'https://hst-api.wialon.com/wialon/ajax.html'),
    
    /*
    |--------------------------------------------------------------------------
    | Options Guzzle
    |--------------------------------------------------------------------------
    |
    | Options supplémentaires pour le client HTTP Guzzle (timeout, verify, etc.)
    | Exemple :
    | 'guzzle' => [
    |     'timeout' => 30,
    |     'verify' => storage_path('certif/cacert.pem'),
    | ]
    |
    */
    'guzzle' => [
        'timeout' => 30,
        // 'verify' => true,
    ],
];
