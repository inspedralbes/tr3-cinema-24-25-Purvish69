<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;

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
        // Forzar HTTPS en entorno de producción
        if ($this->app->environment('production')) {
            URL::forceScheme('https');
        }

        // Rate Limiters para seguridad
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });

        RateLimiter::for('auth', function (Request $request) {
            return Limit::perMinute(5)->by($request->ip());
        });

        RateLimiter::for('holds', function (Request $request) {
            $token = $request->input('hold_token') ?: 'anonymous';
            return [
                Limit::perMinute(20)->by($request->ip()),
                Limit::perMinute(20)->by($token),
            ];
        });

        RateLimiter::for('checkout', function (Request $request) {
            $userOrIp = $request->user()?->id ?: $request->ip();
            return [
                Limit::perMinute(15)->by($request->ip()),
                Limit::perMinute(15)->by($userOrIp),
            ];
        });
    }
}
