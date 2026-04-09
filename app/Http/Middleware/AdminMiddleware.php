<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('user')->check() && (Auth::guard('user')->user()->role === 'admin')) {
            return $next($request);
        }

        // If logged in but not admin, log them out or just deny access
        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
        }

        return redirect()->route('user.login')->with('error', 'Accès réservé à l\'administrateur.');
    }
}
