<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * SecurityHeaders — Adds HTTP security headers to every response.
 * Protects against clickjacking, XSS, MIME-sniffing, and information leakage.
 */
class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Prevent site from being embedded in an iframe (clickjacking)
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // Prevent browser from MIME-type sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // Enable browser XSS filtering
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // Control how much referrer info is included
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // Permissions Policy — disable access to sensitive browser features
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=()');

        // Remove server identity header if present
        $response->headers->remove('X-Powered-By');
        $response->headers->remove('Server');

        // Production-only headers
        if (app()->environment('production')) {
            // HSTS — enforce HTTPS for 1 year
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');

            // Strict CSP for production
            $csp = implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval'",
                "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",
                "font-src 'self' https://fonts.gstatic.com",
                "img-src 'self' data: blob:",
                "connect-src 'self'",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "form-action 'self'",
            ]);
            $response->headers->set('Content-Security-Policy', $csp);
        }
        // In local/development: skip CSP so Vite dev server (localhost:5173) can serve assets freely

        return $response;
    }
}
