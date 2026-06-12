<?php

use App\Http\Middleware\CheckOwnership;
use App\Http\Middleware\CheckPermission;
use App\Http\Middleware\HandleInertiaRequests;
use App\Http\Middleware\QueryCountMiddleware;
use App\Providers\SlowQueryServiceProvider;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Middleware\AddLinkHeadersForPreloadedAssets;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web(append: [
            HandleInertiaRequests::class,
            AddLinkHeadersForPreloadedAssets::class,
            QueryCountMiddleware::class,
        ]);

        $middleware->alias([
            'permission' => CheckPermission::class,
            'ownership' => CheckOwnership::class,
        ]);
    })
    ->withProviders([
        SlowQueryServiceProvider::class,
    ])
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->shouldRenderJsonWhen(
            fn (Request $request) => $request->is('api/*'),
        );
    })->create();
