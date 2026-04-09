<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ForcePasswordChange
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::guard('user')->user();

        if ($user && $user->role === 'assure' && $user->must_change_password) {
            // Éviter la boucle infinie : laisser passer les routes de changement de mot de passe
            if (!$request->routeIs('assure.password.change', 'assure.password.update', 'assure.logout')) {
                return redirect()->route('assure.password.change')
                    ->with('info', 'Veuillez changer votre mot de passe avant de continuer.');
            }
        }

        return $next($request);
    }
}
