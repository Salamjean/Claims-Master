<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HopitalMiddleware
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
            if (in_array($user->role, ['hopital', 'groupe'])) {
                return $next($request);
            }
        }

        return redirect()->route('portal.login')->with('error', 'Accès réservé au personnel médical.');
    }
}
