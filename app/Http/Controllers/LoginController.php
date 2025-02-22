<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Services\FirestoreService;

use Illuminate\Support\Facades\Hash;

class LoginController extends Controller
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

    public function index()
    {
        return view('login.login');
    }


    public function authenticate(Request $request)
    {
        $credentials = $request->only('email', 'password');
        $user = $this->realtimeDatabaseService->getUserByEmail($credentials['email'] ?? null);
      // dd($this->verifyPassword($credentials['password'], $user['password']));
        if (!$user || !$this->verifyPassword($credentials['password'], $user['password'])) {
            return back()->withErrors([
                'email' => 'Les identifiants fournis ne sont pas corrects.',
            ]);
        }

        // Démarrer la session utilisateur
        $request->session()->put('user', [
            'id' => $user['id'],
            'email' => $user['email'],
            'lastname' => $user['nom'],
            'firstname' => $user['prenoms'],
            'role' => $user['role'],
            'status' => $user['status'],
        ]);

        return redirect()->intended('/home');
    }
}
