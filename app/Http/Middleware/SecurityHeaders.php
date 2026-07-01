<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Security headers to be set.
     *
     * @var array
     */
    private array $headers = [
        'X-Frame-Options' => 'SAMEORIGIN',
        'X-Content-Type-Options' => 'nosniff',
        'Referrer-Policy' => 'strict-origin-when-cross-origin',
        'Permissions-Policy' => 'geolocation=(), microphone=(), camera=()',
        'Cross-Origin-Opener-Policy' => 'same-origin',
        'Cross-Origin-Resource-Policy' => 'same-origin',
        'X-Permitted-Cross-Domain-Policies' => 'none',
        'Content-Security-Policy' => "default-src 'self'; base-uri 'self'; object-src 'none'; frame-ancestors 'self'; form-action 'self'; img-src 'self' data: blob: https:; font-src 'self' data: https://fonts.gstatic.com; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; script-src 'self' 'unsafe-inline' https://www.google.com/recaptcha/ https://www.gstatic.com/recaptcha/; connect-src 'self' https://www.google.com/recaptcha/; frame-src 'self' https://www.google.com/recaptcha/ https://www.google.com/maps/",
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $headers = $this->headers;

        // In local/dev environment, skip CSP header to avoid blocking Vite dev server and HMR.
        if (app()->environment('local')) {
            unset($headers['Content-Security-Policy']);
        } else {
            // Prefer IPv4/localhost for dev server sources to avoid IPv6 literal parsing issues in CSP
            $viteHosts = ['http://127.0.0.1:5173', 'http://localhost:5173'];
            $viteWs = ['ws://127.0.0.1:5173', 'ws://localhost:5173'];

            $policy = $headers['Content-Security-Policy'] ?? '';

            // Parse policy into directive => value map
            $directives = [];
            foreach (array_filter(array_map('trim', explode(';', $policy))) as $part) {
                if (preg_match('/^([a-zA-Z-]+)\s+(.*)$/', $part, $m)) {
                    $directives[$m[1]] = $m[2];
                }
            }

            // Helpers to append sources if not present
            $appendSources = function (&$directives, $name, array $sources) {
                $existing = $directives[$name] ?? '';
                foreach ($sources as $s) {
                    if ($existing === '' || strpos($existing, $s) === false) {
                        $existing = trim($existing . ' ' . $s);
                    }
                }
                $directives[$name] = $existing;
            };

            // Add Vite script host to script-src
            $appendSources($directives, 'script-src', $viteHosts);
            // Add Vite HMR websocket and http to connect-src
            $appendSources($directives, 'connect-src', array_merge($viteHosts, $viteWs));
            // Add Vite to style-src and fonts to font-src
            $appendSources($directives, 'style-src', $viteHosts);
            $appendSources($directives, 'font-src', ['https://fonts.bunny.net']);

            // Rebuild policy string
            $newParts = [];
            foreach ($directives as $k => $v) {
                $newParts[] = $k . ' ' . trim($v);
            }
            $headers['Content-Security-Policy'] = implode('; ', $newParts);
        }

        foreach ($headers as $key => $value) {
            $response->headers->set($key, $value);
        }

        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains');
        }

        return $response;
    }
}
