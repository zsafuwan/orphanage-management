<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CareAccess
{
    public function handle(Request $request, Closure $next)
    {
        // The existing portal has no staff authentication yet. Keep database access local.
        abort_unless(app()->environment('local') && in_array($request->ip(), ['127.0.0.1', '::1'], true), 403, 'Database entry is currently available on this computer only.');

        return $next($request);
    }
}
