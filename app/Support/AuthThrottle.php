<?php

namespace App\Support;

use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

class AuthThrottle
{
    /**
     * Limpia el contador de intentos de login para un email+IP.
     *
     * La clave debe replicar exactamente la que construye el limiter 'login'
     * en FortifyServiceProvider::configureRateLimiting() una vez pasada por
     * Illuminate\Routing\Middleware\ThrottleRequests (que antepone el nombre
     * del limiter y aplica md5 a la clave resultante). El helper de Fortify
     * (Laravel\Fortify\LoginRateLimiter::clear()) NO limpia esta misma clave
     * -no aplica ese prefijo/hash-, por lo que su "reset al loguear bien" no
     * tiene efecto real en este proyecto; por eso se centraliza aquí.
     */
    public static function clear(string $email, string $ip): void
    {
        $throttleKey = Str::transliterate(Str::lower($email).'|'.$ip);

        RateLimiter::clear(md5('login'.$throttleKey));
    }
}
