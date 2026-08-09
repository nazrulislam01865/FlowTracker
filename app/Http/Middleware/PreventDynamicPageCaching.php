<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Dynamic FlowTrack HTML contains per-session CSRF tokens and user-specific
 * content. It must never be cached by a browser proxy/CDN as reusable HTML.
 */
class PreventDynamicPageCaching
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        $contentType = strtolower((string) $response->headers->get('Content-Type'));

        if (str_contains($contentType, 'text/html')) {
            $response->headers->set('Cache-Control', 'private, no-store, no-cache, must-revalidate, max-age=0');
            $response->headers->set('Pragma', 'no-cache');
            $response->headers->set('Expires', '0');
        }

        return $response;
    }
}
