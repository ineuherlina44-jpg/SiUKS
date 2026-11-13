<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;

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
        RateLimiter::for('login', function (Request $request) {
            $input = $request->input('identifier', '');
            $identifier = is_string($input)
                ? Str::transliterate(Str::lower($input))
                : '';

            return [
                Limit::perMinute(5)->by('login:'.$request->ip().':'.$identifier),
                Limit::perMinute(100)->by('login-ip:'.$request->ip()),
            ];
        });

        RateLimiter::for('registration', function (Request $request) {
            return Limit::perMinute(20)->by('registration:'.$request->ip());
        });
    }
}
