<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class LogActivity
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        if ($request->user()) {
            Log::channel('activity')->info('User activity', [
                'user_id' => $request->user()->id,
                'method' => $request->method(),
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
        }

        return $response;
    }
}
