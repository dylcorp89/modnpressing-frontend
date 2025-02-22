<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserActive
{

    public function handle(Request $request, Closure $next)
    {
        // Vérifie si l'utilisateur est authentifié et actif
        if (Auth::check() && Auth::user()->is_active) {
            return $next($request); // Autoriser l'accès à la page
        }

        // Rediriger l'utilisateur vers la page d'accueil s'il n'est pas actif
        return redirect('/');
    }


}
