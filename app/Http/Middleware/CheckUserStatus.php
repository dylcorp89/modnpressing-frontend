<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckUserStatus
{
    public function handle(Request $request, Closure $next)
    {
        // Récupérer l'utilisateur de la requête
        $user = $request->get('authenticated_user');

        // Vérifier si l'utilisateur existe
        if (!$user) {
            return redirect()->route('login')->with('error', 'Utilisateur introuvable dans la base de données.');
        }

        // Vérifier si le statut de l'utilisateur est "actif"
        if (isset($user['status']) && $user['status'] !== 'actif') {
            return redirect()->route('login')->with('error', 'Votre compte est inactif. Veuillez contacter l\'administrateur.');
        }

        // L'utilisateur est valide, passer la requête au middleware suivant
        return $next($request);
    }
}
