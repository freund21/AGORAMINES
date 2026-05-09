<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

// BASE LARAVEL:
// Service Provider base de Laravel.
// Es un punto global para registrar configuraciones o servicios.
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // BASE LARAVEL:
        // register(): se usa para enlazar servicios en el contenedor de Laravel.
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // BASE LARAVEL:
        // boot(): se ejecuta al arrancar la aplicación.
        // Aquí podrías poner ajustes globales de modelos, validaciones, etc.
        //
    }
}
