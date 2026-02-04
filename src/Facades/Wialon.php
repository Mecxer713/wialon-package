<?php

namespace Mecxer\WialonPackage\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \Mecxer\WialonPackage\WialonClient login()
 * @method static array call(string $svc, array $params = [], ?string $method = null)
 * @method static array callPost(string $svc, array $params = [])
 * @method static array callGet(string $svc, array $params = [])
 * @method static void setVerifyPath(string $path)
 * @method static string|null getSessionId()
 * @method static array getUnits(int $flags = 1)
 * @see \Mecxer\WialonPackage\WialonClient
 */
class Wialon extends Facade
{
    /**
     * Retourne le nom de la liaison (binding) dans le conteneur de services.
     * Cela doit correspondre à ce que nous avons défini dans le ServiceProvider.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        // On pointe vers l'alias qu'on a défini dans WialonServiceProvider
        return 'wialon'; 
    }
}
