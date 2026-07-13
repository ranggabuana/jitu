<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // Security headers
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'geolocation=(), microphone=(), camera=(), payment=(), usb=(), magnetometer=(), gyroscope=(), accelerometer=()');
        $response->headers->set('X-Permitted-Cross-Domain-Policies', 'none');

        // Content-Security-Policy (hanya aktif di production, bukan di development)
        // Di development, Vite dev server menggunakan alamat dinamis (http://[::1]:5173, ws://, dll.)
        // yang sulit di-whitelist secara konsisten di semua browser
        if (!app()->environment('local', 'development', 'dev')) {
            $csp = implode('; ', [
                // Default fallback: only allow same origin
                "default-src 'self'",

                // Scripts: self + CDN sources + inline/eval needed by Tailwind CDN, CKEditor, TinyMCE, SweetAlert2
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' cdn.jsdelivr.net cdn.tailwindcss.com cdn.ckeditor.com cdnjs.cloudflare.com code.jquery.com",

                // Styles: self + CDN sources + inline styles (Tailwind, component styles)
                "style-src 'self' 'unsafe-inline' cdn.jsdelivr.net cdnjs.cloudflare.com fonts.googleapis.com",

                // Images: self + data URIs (base64 images) + blob (file previews) + https (external images)
                "img-src 'self' data: blob: https:",

                // Fonts: self + Google Fonts + Font Awesome + MDI icons
                "font-src 'self' fonts.gstatic.com cdnjs.cloudflare.com cdn.jsdelivr.net data:",

                // Connections (AJAX/fetch): self only
                "connect-src 'self'",

                // Frames: self only (PDF viewer iframes)
                "frame-src 'self' blob:",

                // Media: self only
                "media-src 'self'",

                // Objects (Flash, Java, etc): none
                "object-src 'none'",

                // Base URI: self only (prevent base tag injection)
                "base-uri 'self'",

                // Form actions: self only
                "form-action 'self'",

                // Frame ancestors: self only (same as X-Frame-Options SAMEORIGIN)
                "frame-ancestors 'self'",
            ]);

            $response->headers->set('Content-Security-Policy', $csp);
        }

        return $response;
    }
}
