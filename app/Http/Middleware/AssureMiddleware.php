<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AssureMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::guard('user')->check() && Auth::guard('user')->user()->role === 'assure') {
            return $next($request);
        }

        if (Auth::guard('user')->check()) {
            Auth::guard('user')->logout();
        }

        return redirect()->route('login')->with('error', 'Accès réservé aux assurés.');
    }
}
