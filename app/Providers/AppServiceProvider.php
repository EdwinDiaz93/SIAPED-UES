<?php

namespace App\Providers;

use App\Support\AuthThrottle;
use Carbon\CarbonImmutable;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
        $this->configureAuthThrottleReset();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }

    /**
     * Limpia el contador de intentos de login (clave email+IP) al cerrar
     * sesión o iniciar sesión correctamente, para que un bloqueo previo no
     * "sobreviva" innecesariamente tras una autenticación exitosa.
     */
    protected function configureAuthThrottleReset(): void
    {
        Event::listen(function (Login $event) {
            AuthThrottle::clear($event->user->email, request()->ip());
        });

        Event::listen(function (Logout $event) {
            if ($event->user) {
                AuthThrottle::clear($event->user->email, request()->ip());
            }
        });
    }
}
