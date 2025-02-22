<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use App\Services\FirestoreService;
use Kreait\Firebase\Contract\Auth;
use Kreait\Firebase\Messaging\CloudMessage;

class CommandesController extends Controller
{
    //
    //
    protected $realtimeDatabaseService;
    protected $database;
    //
    public function __construct(FirestoreService $realtimeDatabaseService)
    {
        $this->realtimeDatabaseService = $realtimeDatabaseService;
    }


    public function index()
    {
        // try {
        //     $commandes = $this->realtimeDatabaseService->getData('commandes') ?? [];
        //     $stat = $this->realtimeDatabaseService->getCommandesCount() ?? [];

        //     // Transformation des données en tableau avec clé `id` pour compatibilité avec la vue
        //     $formattedUsers = [];
        //     foreach ($commandes as $id => $commande) {
        //         $formattedUsers[] = array_merge(['id' => $id], $commande);
        //     }

        //     // Pagination
        //     $page = $_GET['page'] ?? 1; // Numéro de page actuel (par défaut 1)
        //     $itemsPerPage = 50; // Nombre d'éléments par page
        //     $totalItems = count($formattedUsers); // Nombre total d'éléments
        //     $totalPages = ceil($totalItems / $itemsPerPage); // Nombre total de pages

        //     // Découper les données pour la page actuelle
        //     $offset = ($page - 1) * $itemsPerPage;
        //     $paginatedData = array_slice($formattedUsers, $offset, $itemsPerPage);

        //     // Passer les données et les informations de pagination à la vue
        //     return view('dashboard.commandes', [
        //         'commandes' => $paginatedData,
        //         'currentPage' => $page,
        //         'totalPages' => $totalPages,
        //         'totalItems' => $totalItems,
        //     ]);
        // } catch (Exception $e) {
        //     return view('dashboard.commandes')->with(['error' => $e->getMessage()]);
        // }
        return view('dashboard.commandes');
    }

    // Method to apply points and reduce them to zero
    public function applyPoints(Request $request)
    {
        // Validate the user ID
        $request->validate([
            'user_id' => 'required',
        ]);

        // Get the user ID from the request
        $userId = $request->user_id;
        $dataUser = [
            'point' => 0,
            'utilisation'=> "Non"
        ]; // Data to set the user's points to 0

        try {
            // Fetch user data using FirestoreService to ensure user exists and get their current data
            $userData = $this->realtimeDatabaseService->getData("users/$userId");

            if ($userData && isset($userData['info'])) {
                // Update the 'point' field to 0 for the specific user in Firebase Realtime Database
                $this->realtimeDatabaseService->updateData("users/$userId/info", $dataUser, "");

                // Redirect with success message
                return redirect()->back()->with('success', 'Les points ont été appliqués et réduits à zéro.');
            } else {
                // User not found or info not available
                return redirect()->back()->with('error', 'Utilisateur non trouvé ou les informations sont manquantes.');
            }
        } catch (Exception $e) {
            // Handle the exception and return error message
            return redirect()->back()->with('error', 'Erreur lors de l\'application des points : ' . $e->getMessage());
        }
    }


    public function traitement(Request $request, $id)
    {
        // Validation des données reçues
        $validated = $request->validate([
            'traitement' => 'required', // Assurez-vous que le champ "traitement" est rempli
        ]);

        $statut = $validated['traitement'];

        // Récupérer la commande correspondante
        $commande = $this->realtimeDatabaseService->getData("commandes/$id");

        if ($statut == "Paid" && $commande) {
            $IdUser = $commande['iduser']; // ID de l'utilisateur associé à la commande
            $prix = $commande['prix']; // Prix de la commande
            $nouveauxPoints = $prix / 1000; // Calcul des nouveaux points de fidélité

            // Récupérer les informations utilisateur
            $userData = $this->realtimeDatabaseService->getData("users/$IdUser/info");

            if ($userData) {
                // Récupérer les points existants, ou 0 si non définis
                $pointsActuels = $userData['point'] ?? 0;

                // Calculer le total des points
                $pointsTotaux = $pointsActuels + $nouveauxPoints;

                // Mettre à jour les points de fidélité de l'utilisateur
                $dataUser = [
                    'point' => $pointsTotaux, // Mise à jour des points
                ];

                try {
                    // Mettre à jour les points de fidélité dans la section "info"

                    $this->realtimeDatabaseService->updateData("users/$IdUser/info", $dataUser, "");
                } catch (Exception $e) {
                    // Gestion de l'erreur
                    throw new Exception("Erreur lors de la mise à jour des points : " . $e->getMessage());
                }

                //$this->realtimeDatabaseService->updateData("users/$IdUser/info", $dataUser,$IdUser);
            }
        }

        // Mettre à jour le statut de la commande dans la base de données
        $dataCommande = [
            'statut' => $statut, // Mise à jour du statut
        ];

        $this->realtimeDatabaseService->updateData("commandes/", $dataCommande, $id);

        // Récupérer le token de notification du client
        $clientToken = $commande['tokenId'] ?? null;

        if ($clientToken) {
            // Préparer le titre et le message en fonction du statut
            $title = "";
            $message = match ($statut) {
                'Traitement' => "Votre commande est en cours de traitement.",
                'Completed' => "Votre linge est prêt. Merci de votre confiance !",
                'Paid' => "Votre paiement pour la commande N°" . $commande['identifiant'] . " a bien été pris en compte. Merci de votre confiance !",
                default => "Un chauffeur est en route pour la collecte",
            };

            // Envoyer la notification
            $this->sendNotification($clientToken, $title, $message);
        }

        return redirect()->back();
    }









    public function prix(Request $request, $id)
    {

        // Validation des données reçues
        $validated = $request->validate([
            'prix' => 'required|numeric', // Assurez-vous que le prix est un nombre valide
        ]);

        //dd($validated, $id);
        $prix = $validated['prix'];

        $prix = intval($prix);
        // Créer un tableau avec les données que vous souhaitez mettre à jour
        $data = [
            'prix' => $prix,  // Champ existant que vous souhaitez mettre à jour
            'statut' => 'Traitement', // Nouveau champ que vous ajoutez
        ];

        $commandes = $this->realtimeDatabaseService->updateData("commandes", $data, $id) ?? [];
        //dd($commandes);

        return  redirect()->back();
    }


    public function details($id)
    {
        // Récupérer les détails de la commande
        $commandes = $this->realtimeDatabaseService->getAllFieldsById("commandes", $id) ?? [];

        // Vérifier si le champ `iduser` existe
        if (!isset($commandes['iduser'])) {
            throw new Exception("L'identifiant utilisateur (iduser) est manquant dans la commande.");
        }

        // Chemin des credentials Firebase
        $path = storage_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
        }

        // Initialisation de Firebase Admin SDK
        $firebase = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

        $this->database = $firebase->createAuth();

        // Vérifier si `iduser` est un UID valide ou un UUID généré
        $userRecord = null;
        $moreUserInfo = null;

        try {
            // Si `iduser` semble être un UID Firebase, récupérer l'utilisateur
            if (!preg_match('/^[a-f0-9\-]{36}$/', $commandes['iduser'])) {
                $userRecord = $this->database->getUser($commandes['iduser']);
                // Récupérer plus d'informations sur l'utilisateur dans la base de données
                $moreUserInfo = $this->realtimeDatabaseService->getAllFieldsById("users", $commandes['iduser']) ?? [];
            }
        } catch (\Exception $e) {
            // Gestion des utilisateurs non connectés (ou UID invalide)
            $userRecord = [
                'uid' => $commandes['iduser'],
                'displayName' => 'Utilisateur non connecté',
                'email' => 'N/A'
            ];
        }

        // Retourner la vue avec les informations nécessaires
        return view('dashboard.commandes.index', [
            'commandes' => $commandes,
            'user' => $userRecord,
            'id' => $id,
            'userMore' => $moreUserInfo,
        ]);
    }


    public function details2($id)
    {
        // Récupérer les détails de la commande
        $commandes = $this->realtimeDatabaseService->getAllFieldsByField("commandes","identifiant", $id) ?? [];

        // Vérifier si le champ `iduser` existe
        if (!isset($commandes['iduser'])) {
            throw new Exception("L'identifiant utilisateur (iduser) est manquant dans la commande.");
        }

        // Chemin des credentials Firebase
        $path = storage_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
        }

        // Initialisation de Firebase Admin SDK
        $firebase = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

        $this->database = $firebase->createAuth();

        // Vérifier si `iduser` est un UID valide ou un UUID généré
        $userRecord = null;
        $moreUserInfo = null;

        try {
            // Si `iduser` semble être un UID Firebase, récupérer l'utilisateur
            if (!preg_match('/^[a-f0-9\-]{36}$/', $commandes['iduser'])) {
                $userRecord = $this->database->getUser($commandes['iduser']);
                // Récupérer plus d'informations sur l'utilisateur dans la base de données
                $moreUserInfo = $this->realtimeDatabaseService->getAllFieldsById("users", $commandes['iduser']) ?? [];
            }
        } catch (\Exception $e) {
            // Gestion des utilisateurs non connectés (ou UID invalide)
            $userRecord = [
                'uid' => $commandes['iduser'],
                'displayName' => 'Utilisateur non connecté',
                'email' => 'N/A'
            ];
        }

        // Retourner la vue avec les informations nécessaires
        return view('dashboard.commandes.index', [
            'commandes' => $commandes,
            'user' => $userRecord,
            'id' => $id,
            'userMore' => $moreUserInfo,
        ]);
    }

    public function sendNotification($token, $title, $body)
{
    // Chemin vers le fichier JSON des credentials Firebase
    $serviceAccount = storage_path(env('FIREBASE_CREDENTIALS'));

    try {
        // Initialisation de Firebase
        $firebase = (new Factory)
            ->withServiceAccount($serviceAccount);

        $messaging = $firebase->createMessaging();

        // Construire le message
        $message = CloudMessage::withTarget('token', $token)
            ->withNotification([
                'title' => $title,
                'body' => $body,
            ]);

        // Envoyer la notification
        $messaging->send($message);

        return response()->json(['message' => 'Notification envoyée avec succès']);
    } catch (\Kreait\Firebase\Exception\Messaging\NotFound $e) {
        return response()->json(['error' => 'Jeton introuvable ou invalide'], 404);
    } catch (\Throwable $th) {
        return response()->json(['error' => $th->getMessage()], 500);
    }
}




}
