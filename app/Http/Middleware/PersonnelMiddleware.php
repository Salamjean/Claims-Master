<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PersonnelMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        if (auth('user')->check() && auth('user')->user()->role === 'personnel') {
            return $next($request);
        }

        return redirect()->route('portal.login')->with('error', 'Accès réservé au personnel d\'assurance.');
    }
}
