<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->redirectGuestsTo(function (Illuminate\Http\Request $request) {
            if ($request->is('admin/*') || $request->is('assurance/*') || $request->is('police/*') || $request->is('gendarmerie/*')) {
                return route('portal.login');
            }
            return route('login');
        });
        $middleware->validateCsrfTokens(except: [
            'webhook/wave',
        ]);

        $middleware->alias([
            'admin' => \App\Http\Middleware\AdminMiddleware::class,
            'assurance' => \App\Http\Middleware\AssuranceMiddleware::class,
            'assure' => \App\Http\Middleware\AssureMiddleware::class,
            'police' => \App\Http\Middleware\PoliceMiddleware::class,
            'gendarmerie' => \App\Http\Middleware\GendarmerieMiddleware::class,
            'agent' => \App\Http\Middleware\AgentMiddleware::class,
            'force.password' => \App\Http\Middleware\ForcePasswordChange::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
