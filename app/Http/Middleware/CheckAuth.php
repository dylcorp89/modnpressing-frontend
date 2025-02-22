<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Vérifie si le token existe dans l'en-tête Authorization
        $lastname = session('user.lastname');


        if (!$lastname) {
            // Si le token n'est pas présent, redirige vers la page de connexion
            return redirect()->route('login')->with('error', 'Vous devez vous connecter pour accéder à cette section.');
        }



        return $next($request);
    }
}
