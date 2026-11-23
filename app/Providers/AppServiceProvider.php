<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\ServiceProvider;
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
        RateLimiter::for('admin-login', static function (Request $request): array {
            $email = Str::of((string) $request->input('email', ''))->trim()->lower()->value();

            return [
                Limit::perMinute(5)->by('admin-login:email:'.hash('sha256', $email)),
                Limit::perMinute(30)->by('admin-login:ip:'.$request->ip()),
            ];
        });

        if (config('app.env') === 'production') {
            URL::forceScheme('https');
        }
    }
}
