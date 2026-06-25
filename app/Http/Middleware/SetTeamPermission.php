<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SetTeamPermission — now a passthrough since we use OrganizationScope
 * for multi-tenancy instead of Spatie Teams feature.
 * Kept for future extensibility.
 */
class SetTeamPermission
{
    public function handle(Request $request, Closure $next): Response
    {
        return $next($request);
    }
}
