<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureOrganizationConfigured
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth()->user();

        if ($user && $user->isAdmin() && $user->organization) {
            if (!$user->organization->isConfigured()) {
                // Allow access to profile setup routes
                if (!$request->routeIs('admin.organization.*')) {
                    return redirect()->route('admin.organization.setup')
                        ->with('warning', 'Lengkapi profil koperasi terlebih dahulu untuk mengaktifkan semua fitur.');
                }
            }
        }

        return $next($request);
    }
}
