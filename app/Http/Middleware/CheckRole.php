<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        if (!in_array(Auth::user()->role, $roles)) {
            if (Auth::user()->role === 'pemohon') {
                return redirect()->route('pemohon.dashboard')
                    ->with('error', 'Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.');
            }

            return redirect()->route('dashboard')
                ->with('error', 'Akses ditolak. Anda tidak memiliki hak akses ke halaman tersebut.');
        }

        return $next($request);
    }
}
