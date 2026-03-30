<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Define allowed roles for admin dashboard
        $allowedRoles = [
            'admin',
            'fo',
            'bo',
            'operator_opd',
            'kepala_opd',
            'verifikator',
            'kadin'
        ];

        // Check if user has allowed role
        if (!in_array(Auth::user()->role, $allowedRoles)) {
            // Redirect pemohon to their own dashboard
            if (Auth::user()->role === 'pemohon') {
                return redirect()->route('pemohon.dashboard')
                    ->with('error', 'Akses ditolak. Anda dialihkan ke dashboard pemohon.');
            }

            // For other unauthorized roles
            return redirect()->back()
                ->with('error', 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}
