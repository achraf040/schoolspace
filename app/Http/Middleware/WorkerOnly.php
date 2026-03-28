<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class WorkerOnly
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Vérifier que l'utilisateur n'est pas un admin et qu'il est actif
        if ($user->isAdmin() || !$user->is_active) {
            Auth::logout();
            return redirect()->route('login')->withErrors(['email' => 'Accès non autorisé.']);
        }

        // Vérifier que l'utilisateur a au moins un espace attribué
        if ($user->espaces()->count() === 0) {
            // Essayer l'attribution automatique basée sur l'email
            $user->autoAssignEspace();
            
            // Vérifier à nouveau après tentative d'attribution
            if ($user->espaces()->count() === 0) {
                Auth::logout();
                return redirect()->route('login')->withErrors(['email' => 'Aucun espace assigné à votre compte. Veuillez contacter l\'administrateur.']);
            }
        }

        // Log de sécurité (optionnel)
        Log::info('Worker access', [
            'user_id' => $user->id,
            'email' => $user->email,
            'spaces_count' => $user->espaces()->count(),
            'ip' => $request->ip(),
            'user_agent' => $request->userAgent()
        ]);

        return $next($request);
    }
}