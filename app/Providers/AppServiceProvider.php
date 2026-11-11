<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        // Starting, finishing and submitting rounds: plenty for real play, not for scripting.
        RateLimiter::for('rounds', fn (Request $request) => Limit::perMinute(30)->by((string) $request->ip()));

        // Live scoring while painting (the game sends at most about 5 a second).
        RateLimiter::for('painting', fn (Request $request) => Limit::perMinute(360)->by((string) $request->ip()));
    }
}
