<?php

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->statefulApi();
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->render(function(AuthenticationException $e) {
            return response()->json([
                'succeed' => false,
                'messages' => ['Anda tidak memiliki akses menuju halaman ini'],
                'data' => null
            ]);
        });

        $exceptions->render(function (ValidationException $e) {
            return response()->json([
                'succeed' => false,
                'messages' => [$e->getMessage()],
                'data' => null
            ]);
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            return response()->json([
                'succeed' => false,
                'messages' => ['Data tidak ditemukan'],
                'data' => null
            ]);
        });

        // $exceptions->render(function (Throwable $e) {
        //     return response()->json([
        //         'succeed' => false,
        //         'messages' => ['Terjadi kesalahan pada server, silakan coba lagi nanti'],
        //         'data' => null
        //     ]);
        // });
    })->create();
