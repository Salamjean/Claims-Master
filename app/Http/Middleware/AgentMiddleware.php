<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AgentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('user')->check() && auth('user')->user()->role === 'agent') {
            return $next($request);
        }

        return redirect()->route('login')->with('error', 'Accès réservé aux agents de constat.');
    }
}
