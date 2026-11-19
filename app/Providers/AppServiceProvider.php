<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
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
        Gate::before(function ($user, string $ability) {
            return method_exists($user, 'hasRole') && $user->hasRole('super_admin') ? true : null;
        });

        RateLimiter::for('otp', function (Request $request): array {
            return [
                Limit::perMinute(3)->by('otp-phone:'.preg_replace('/\D/', '', (string) $request->input('phone'))),
                Limit::perMinute(10)->by('otp-ip:'.$request->ip()),
            ];
        });
    }
}
