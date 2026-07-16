<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\AuthServiceProvider::class,
    App\Providers\EventServiceProvider::class,

    // Domain-specific providers for the NGO system
    App\Providers\DonationServiceProvider::class,   // binds payment gateway interfaces
    App\Providers\ProjectServiceProvider::class,    // project/report generation bindings
    App\Providers\ReportingServiceProvider::class,  // PDF/Excel report generation bindings
];
<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsStaff
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || ! in_array($user->role, ['staff', 'admin'])) {
            abort(403, 'This area is restricted to staff members.');
        }

        return $next($request);
    }
}

<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsDonor
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (! $user || $user->role !== 'donor') {
            abort(403, 'This area is restricted to registered donors.');
        }

        return $next($request);
    }
}

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