<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDonor
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! $request->user() || ! $request->user()->isDonor()) {
            abort(403, 'This area is restricted to registered donors.');
        }

        return $next($request);
    }
}
