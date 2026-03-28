<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\HttpFoundation\Response;

class LoginRateLimit
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $key = 'login.' . $request->ip();
        
        // Limiter à 5 tentatives par minute
        if (RateLimiter::tooManyAttempts($key, 5)) {
            $seconds = RateLimiter::availableIn($key);
            
            return back()->withErrors([
                'email' => "Trop de tentatives de connexion. Réessayez dans {$seconds} secondes."
            ])->withInput($request->except('password'));
        }
        
        $response = $next($request);
        
        // Si la connexion échoue, incrémenter le compteur
        if ($response->getStatusCode() === 302 && session()->has('errors')) {
            RateLimiter::hit($key, 60); // 60 secondes
        } else {
            // Si la connexion réussit, effacer le compteur
            RateLimiter::clear($key);
        }
        
        return $response;
    }
}