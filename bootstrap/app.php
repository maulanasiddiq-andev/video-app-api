<?php

use App\Http\Resources\BaseResponse;
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
            $base_response = new BaseResponse(false, ['Anda tidak memiliki akses menuju halaman ini'], null);
            return response()->json($base_response->toArray(), 401);
        });

        $exceptions->render(function (ValidationException $e) {
            $base_response = new BaseResponse(false, [$e->getMessage()], null);
            return response()->json($base_response->toArray(), 422);
        });

        $exceptions->render(function (NotFoundHttpException $e) {
            $base_response = new BaseResponse(false, ['Data tidak ditemukan'], null);
            return response()->json($base_response->toArray(), 404);
        });

        // $exceptions->render(function (Throwable $e) {
        //     return response()->json([
        //         'succeed' => false,
        //         'messages' => ['Terjadi kesalahan pada server, silakan coba lagi nanti'],
        //         'data' => null
        //     ]);
        // });
    })->create();
