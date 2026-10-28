<?php

namespace App\Providers;

use Anthropic\Client;
use Anthropic\RequestOptions;
use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Database\Eloquent\Model;
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
        $this->app->singleton(Client::class, function (): Client {
            $timeout = (float) config('services.anthropic.timeout');

            return new Client(
                apiKey: (string) config('services.anthropic.api_key'),
                requestOptions: RequestOptions::with(
                    timeout: $timeout,
                    maxRetries: (int) config('services.anthropic.max_retries'),
                    // Das SDK setzt Timeouts nicht selbst durch, deshalb ein Guzzle-Client mit festen Grenzen.
                    transporter: new GuzzleClient(['timeout' => $timeout, 'connect_timeout' => 10]),
                ),
            );
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Model::preventLazyLoading(! $this->app->isProduction());

        // Auf netcup terminiert nginx das TLS; Links und Assets immer per HTTPS ausliefern.
        if ($this->app->isProduction()) {
            URL::forceScheme('https');
        }

        // Jede Anfrage kostet echtes Geld: pro IP höchstens 5 Rezepte pro Minute und 40 pro Tag.
        RateLimiter::for('recipes', function (Request $request): array {
            $message = 'Langsam! Der Chef kocht nicht im Akkord. Warte kurz und versuch es dann noch einmal.';
            $response = function () use ($message) {
                return response()->json(compact('message'), 429);
            };

            return [
                Limit::perMinute(5)->by('min:'.$request->ip())->response($response),
                Limit::perDay(40)->by('day:'.$request->ip())->response($response),
            ];
        });
    }
}
