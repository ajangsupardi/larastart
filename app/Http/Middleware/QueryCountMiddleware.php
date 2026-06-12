<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response;

class QueryCountMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }

    /**
     * Terminate the middleware after the response is sent.
     */
    public function terminate(Request $request, Response $response): void
    {
        if (! config('app.debug')) {
            return;
        }

        if ($request->is('api/*')) {
            return;
        }

        $queryCount = count(DB::getQueryLog());

        $response->headers->set('X-Query-Count', (string) $queryCount);
    }
}
