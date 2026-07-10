<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationIsActive
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->organization_id) {
            $organization = $user->organization;
            if ($organization && !$organization->is_active) {
                // If trying to logout, allow it
                if ($request->routeIs('logout')) {
                    return $next($request);
                }
                
                // If not already on the suspended page, redirect to it
                if (!$request->routeIs('organization.suspended')) {
                    return redirect()->route('organization.suspended');
                }
            }
        }

        return $next($request);
    }
}
