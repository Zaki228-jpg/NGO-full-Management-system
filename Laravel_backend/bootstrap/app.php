<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use App\Http\Middleware\EnsureUserIsAdmin;
use App\Http\Middleware\EnsureUserIsStaff;
use App\Http\Middleware\EnsureUserIsDonor;
use App\Http\Middleware\LogActivity;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        // Register named middleware aliases used across the NGO system
        $middleware->alias([
            'admin' => EnsureUserIsAdmin::class,   // full system access (HR, finance, reports)
            'staff' => EnsureUserIsStaff::class,   // employees managing projects/beneficiaries
            'donor' => EnsureUserIsDonor::class,   // donor portal access only
        ]);

        // Apply activity logging to every web request (audit trail for compliance)
        $middleware->web(append: [
            LogActivity::class,
        ]);

        // Trust proxies if deployed behind a load balancer (e.g. on shared hosting or cloud)
        $middleware->trustProxies(at: '*');

        // Encrypt cookies except ones we intentionally read client-side
        $middleware->encryptCookies(except: [
            'theme', // used for light/dark mode toggle on public site
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // Custom handling for API requests (return JSON instead of HTML error pages)
        $exceptions->render(function (AuthenticationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Unauthenticated. Please log in to continue.',
                ], 401);
            }
        });

        $exceptions->render(function (ValidationException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'The given data was invalid.',
                    'errors' => $e->errors(),
                ], 422);
            }
        });

        $exceptions->render(function (ModelNotFoundException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'The requested resource was not found.',
                ], 404);
            }
        });

        $exceptions->render(function (NotFoundHttpException $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Endpoint not found.',
                ], 404);
            }
        });

        // Report critical errors to logs with extra context (useful when donations/payments fail)
        $exceptions->reportable(function (Throwable $e) {
            if (app()->bound('sentry') && app()->environment('production')) {
                // Hook for external error tracking, if configured later
            }
        });

        // Don't leak stack traces for donation/payment related exceptions in production
        $exceptions->dontReport([
            \App\Exceptions\DonationPaymentException::class,
        ]);
    })->create();