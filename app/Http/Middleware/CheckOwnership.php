<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class CheckOwnership
{
    public function handle(Request $request, Closure $next, string $resource, string $action): Response
    {
        $user = $request->user();

        if (! $user) {
            abort(403, 'Unauthenticated.');
        }

        // Admin bypass: if user has full read permission for the resource, allow
        if ($user->hasPermission($resource, 'read')) {
            return $next($request);
        }

        // Resolve the model from the route parameter
        $paramName = Str::singular($resource);
        $model = $request->route($paramName);

        if (! $model) {
            abort(403, 'Resource not found.');
        }

        // Check ownership: resource's created_by must match the authenticated user's id
        if ($model->created_by !== $user->id) {
            abort(403, 'You do not have permission to modify this resource.');
        }

        return $next($request);
    }
}
