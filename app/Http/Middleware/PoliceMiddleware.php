<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PoliceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = auth('user')->user();
        if (auth('user')->check()) {
            if ($user->role === 'police' || ($user->role === 'agent' && $user->service && $user->service->role === 'police')) {
                return $next($request);
            }
        }

        return redirect()->route('login')->with('error', 'Accès réservé au personnel de la Police.');
    }
}
