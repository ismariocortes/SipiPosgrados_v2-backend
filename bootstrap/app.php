<?php

use App\Support\ApiExceptionResponder;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use App\Http\Middleware\CheckPermission;

return Application::configure(basePath: dirname(__DIR__))

    /*
     * API: prefijo `api` + agrupación `v1` en routes/api.php permite añadir v2, v3…
     * sin duplicar middleware. Las rutas HTTP viven bajo /api/v1/...
     */
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
        apiPrefix: 'api',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // 🔐 Evitar redirect a login (API pura)
        $middleware->redirectGuestsTo(function () {
            return null;
        });

        // 🔥 Registrar tu middleware personalizado
        $middleware->alias([
            'permission' => CheckPermission::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        /*
         * Respuestas JSON uniformes para la API (validación, 404, auth, errores HTTP).
         * Las rutas web siguen usando las vistas/excepciones por defecto de Laravel.
         */
        $exceptions->render(function (Throwable $e, Request $request) {
            if (! $request->is('api/*')) {
                return null;
            }

            return ApiExceptionResponder::toResponse($e);
        });
    })->create();
