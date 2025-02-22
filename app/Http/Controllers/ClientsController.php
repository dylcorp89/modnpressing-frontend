<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Database;
use Kreait\Firebase\Contract\Auth;

class ClientsController extends Controller
{

    /**
     * Initialisation du service Firebase Realtime Database.
     */
    protected $database;

    public function __construct()
    {
        // Chemin des credentials Firebase
        $path = storage_path(env('FIREBASE_CREDENTIALS'));

        if (!file_exists($path)) {
            throw new Exception("Le fichier de configuration Firebase n'existe pas : {$path}");
        }

        // Initialisation de Realtime Database via Firebase Admin SDK
        $firebase = (new Factory)
            ->withServiceAccount($path)
            ->withDatabaseUri(env('FIREBASE_DATABASE_URI'));

        $this->database = $firebase->createAuth();
    }

    /**
     * Affiche les commandes depuis Firebase Realtime Database.
     */
    public function index()
    {
        try {
            // Récupération des données de la référence "user"
            $users = $this->database->listUsers() ?? [];


// Convertir le générateur en tableau
$usersArray = iterator_to_array($users);

// Pagination
$page = $_GET['page'] ?? 1; // Page actuelle
$itemsPerPage = 50; // Nombre d'éléments par page
$totalItems = count($usersArray); // Total des éléments
$totalPages = ceil($totalItems / $itemsPerPage); // Total des pages

// Découper les données pour la page actuelle
$offset = ($page - 1) * $itemsPerPage;
$paginatedData = array_slice($usersArray, $offset, $itemsPerPage);


            // Passage des commandes à la vue "clients.index"

            return view('dashboard.clients',
                [

                    'users' => $paginatedData,
                    'currentPage' => $page,
                    'totalPages' => $totalPages,
                ]
            );
        } catch (Exception $e) {
         return view('dashboard.clients')->with(['error' => $e->getMessage()]);
        }
    }

    /**
     * Affiche le formulaire d'ajout de commande.
     */
    public function create()
    {
        return view('clients.create');
    }

    /**
     * Ajouter une commande dans Firebase Realtime Database.
     */
    public function store(Request $request)
    {

    }

    /**
     * Affiche une commande spécifique.
     */
    public function show($id)
    {

    }

    /**
     * Affiche le formulaire de modification d'une commande.
     */
    public function edit($id)
    {

    }

    /**
     * Mettre à jour une commande dans Firebase Realtime Database.
     */
    public function update(Request $request, $id)
    {

    }

    /**
     * Supprimer une commande dans Firebase Realtime Database.
     */
    public function destroy($id)
    {
    }
}
