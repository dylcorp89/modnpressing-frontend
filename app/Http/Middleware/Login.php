<?php
namespace App\Http\Middleware;



use Closure;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Hash;

class Login
{
    protected $realtimeDatabaseService;

    public function __construct(FirestoreService $realtimeDatabaseService)
    {
        $this->realtimeDatabaseService = $realtimeDatabaseService;
    }

    private function verifyPassword($inputPassword, $storedPassword)
    {
        return Hash::check($inputPassword, $storedPassword);
    }

    public function handle(Request $request, Closure $next)
    {
        $credentials = $request->only('email', 'password');

        $user = $this->realtimeDatabaseService->getUserByEmail($credentials['email'] ?? null);


        if (!$user || !$this->verifyPassword($credentials['password'], $user['password'])) {
            return redirect()->route('login')->withErrors([
                'email' => 'Les identifiants fournis ne sont pas corrects.',
            ]);
        }

        // Ajouter les informations utilisateur au contexte de la requête
        $request->merge(['authenticated_user' => $user]);
//dd($request->all());
        if ($user['status'] !== 'actif') {
            return redirect()->route('login')->with('error', 'Votre compte est inactif. Veuillez contacter l\'administrateur.');
        }

        // Connexion réussie, redirection vers la page principale
        return redirect()->route('home');
    }
}
