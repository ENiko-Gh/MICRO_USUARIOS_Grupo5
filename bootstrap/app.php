<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php', // AÑADIDA: Carga el archivo routes/api.php
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        // El middleware de API ya está configurado en el archivo base (kernel.php o app.php)
        // Puedes añadir aquí middlewares específicos de la API si los necesitas,
        // pero por ahora solo nos aseguramos de que Sanctum esté habilitado para el grupo 'api'
        $middleware->api(prepend: [
             \Laravel\Sanctum\Http\Middleware\AuthenticateSession::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();