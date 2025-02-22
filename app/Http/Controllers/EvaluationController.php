<?php

namespace App\Http\Controllers;
use Exception;
use Illuminate\Http\Request;
use App\Services\FirestoreService;


class EvaluationController extends Controller
{
    //

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
            $users = $this->realtimeDatabaseService->getData('evaluations') ?? [];

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
            return view('dashboard.evaluation', [
                'claims' => $paginatedData,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ]);
        } catch (Exception $e) {
            return view('dashboard.evaluation')->with(['error' => $e->getMessage()]);
        }
    }

        // Method to apply points and reduce them to zero
        public function apply(Request $request)
        {
            // Validate the user ID
            $request->validate([
                'evaluation_id' => 'required',
                'evaluation_statut' => 'required',
            ]);


            // Get the user ID from the request
            $evaluationId = $request->evaluation_id;
            $dataUser = [
                'statut' => "Approuve",

            ]; // Data to set the user's points to 0

            try {

                    // Update the 'point' field to 0 for the specific user in Firebase Realtime Database
                    $this->realtimeDatabaseService->updateData("evaluations/", $dataUser, $evaluationId);

                    // Redirect with success message
                    return redirect()->back()->with('success', 'Approuvé');

            } catch (Exception $e) {
                // Handle the exception and return error message
                return redirect()->back()->with('error', 'Erreur lors de l\'application des points : ' . $e->getMessage());
            }
        }
}
