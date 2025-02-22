<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Exception;

class ReclamationsController extends Controller
{
    //
    protected $realtimeDatabaseService;
    //
    public function __construct(FirestoreService $realtimeDatabaseService)
    {
        $this->realtimeDatabaseService = $realtimeDatabaseService;
    }


    public function index()
    {
        // Paginate the fake data
        try {
            // Récupération des données de la référence "user"
            $users = $this->realtimeDatabaseService->getData('reclamations') ?? [];

            // Transformation des données en tableau avec clé `id` pour compatibilité avec la vue
            $formattedUsers = [];
            foreach ($users as $id => $user) {
                $formattedUsers[] = array_merge(['id' => $id], $user);
            }

            // Pagination
            $page = $_GET['page'] ?? 1; // Numéro de page actuel (par défaut 1)
            $itemsPerPage = 50; // Nombre d'éléments par page
            $totalItems = count($formattedUsers); // Nombre total d'éléments
            $totalPages = ceil($totalItems / $itemsPerPage); // Nombre total de pages

            // Découper les données pour la page actuelle
            $offset = ($page - 1) * $itemsPerPage;
            $paginatedData = array_slice($formattedUsers, $offset, $itemsPerPage);

            // dd($formattedUsers);
            // Passage des commandes à la vue "clients.index"
            return view('dashboard.reclamations', [
                'claims' => $paginatedData,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ]);
        } catch (Exception $e) {
            return view('dashboard.reclamations')->with(['error' => $e->getMessage()]);
        }
    }
}
