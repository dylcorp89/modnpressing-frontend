<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification;
use Exception;

class CampagneController extends Controller
{
    protected $database;
    protected $storage;
    protected $messaging;

    public function __construct()
    {
        // Chemin des credentials Firebase
        $path = storage_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
        }

        // Initialisation de Firebase via Firebase Admin SDK
        $firebase = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

        $this->database = $firebase->createDatabase();
        $this->storage = $firebase->createStorage();
        $this->messaging = $firebase->createMessaging();
    }

    public function index()
    {
        try {
            $campagnesRef = $this->database->getReference('campagnes');
            $campagnes = $campagnesRef->getValue() ?? [];

            return view('campagne', compact('campagnes'));
        } catch (Exception $e) {
            return view('campagne')->with(['error' => 'Erreur lors de la récupération des campagnes : ' . $e->getMessage()]);
        }
    }

    public function store(Request $request)
    {
        // Valider la requête
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'body' => 'required|string',
            'page' => 'required|string',
        ]);

        // Construire la notification
        $notification = Notification::create($validated['title'], $validated['body']);
         $data = ['page' => $validated['page']]; // Données supplémentaires (si nécessaire)

        try {
            // Récupérer les données des utilisateurs depuis Realtime Database
            $usersRef = $this->database->getReference('users');
            $users = $usersRef->getValue();

            if (empty($users)) {
                return redirect()->back()->withErrors(['error' => 'Aucun utilisateur trouvé']);
            }

            // Filtrer les utilisateurs pour récupérer les `fcm_token`
            $tokens = [];
            foreach ($users as $user) {
                if (isset($user['info']['fcm_token']) && !empty($user['info']['fcm_token'])) {
                    // Ajouter uniquement les tokens uniques
                    $tokens[] = $user['info']['fcm_token'];
                }
            }

            // Supprimer les doublons dans le tableau des tokens
            $tokens = array_unique($tokens);

            if (empty($tokens)) {
                return redirect()->back()->withErrors(['error' => 'Aucun token valide trouvé']);
            }

            // Envoyer le message à tous les tokens
            $message = CloudMessage::new()
                ->withNotification($notification)
                ->withData($data);

            $response = $this->messaging->sendMulticast($message, $tokens);
            $campagneRef = $this->database->getReference('campagnes')->push([
                'title' => $validated['title'],
                'body' => $validated['body'],
                'page' => $validated['page'],
                'created_at' => now()->toDateTimeString(),
            ]);


            return redirect()->back()->with([
                'success' => 'Notifications envoyées avec succès.',
                'success_count' => $response->successes()->count(),
                'failure_count' => $response->failures()->count(),
            ]);

        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur : ' . $e->getMessage()]);
        }
    }


    public function destroy($id)
    {
        try {
            $this->database->getReference('campagnes/' . $id)->remove();
            return redirect()->back()->with('success', 'Campagne supprimée avec succès');
        } catch (Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Erreur lors de la suppression : ' . $e->getMessage()]);
        }
    }


}
