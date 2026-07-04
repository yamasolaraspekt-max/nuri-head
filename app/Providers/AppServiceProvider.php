<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
        public function register(): void
        {
            $this->app->singleton(\App\Services\OllamaClient::class, function ($app) {
                return new \App\Services\OllamaClient(
                    host: config('services.ollama.host', env('OLLAMA_HOST', 'http://127.0.0.1:11434')),
                    model: config('services.ollama.model', env('OLLAMA_MODEL', 'llama3:instruct')),
                );
            });

            // (iii-b) Lieferantenkanal-Mapper-Registry (channel-basiert) — Singleton, damit der
            // "kein Mapper"-Debug-Log einmalig pro Lauf bleibt und OMD/DATANORM ohne Eingriff andocken.
            $this->app->singleton(\App\Services\Suppliers\Mappers\MapperRegistry::class);
        }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Paginator::useBootstrap();
        
    }
}
