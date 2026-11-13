<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withCommands([
        __DIR__.'/../app/Console/Commands',
    ])
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->trustProxies(at: '*');

        // Daftarkan alias middleware di dalam sini:
        $middleware->alias([
            'role' => \App\Http\Middleware\RoleMiddleware::class,
            'password.change' => \App\Http\Middleware\RequirePasswordChange::class,
            'credential.no-store' => \App\Http\Middleware\PreventCredentialCaching::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->render(function (\Throwable $exception, Request $request) {
            if (config('app.debug')) {
                return null;
            }

            $status = $exception instanceof HttpExceptionInterface
                ? $exception->getStatusCode()
                : 500;

            if ($status < 500) {
                return null;
            }

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Terjadi kendala pada server. Silakan coba kembali atau hubungi admin.',
                ], $status);
            }

            return response()->view('errors.500', status: $status);
        });
    })->create();
