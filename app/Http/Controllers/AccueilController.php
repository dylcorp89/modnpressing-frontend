<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\FirestoreService;

class AccueilController extends Controller
{
    //
    //
    protected $realtimeDatabaseService;
    //
    public function __construct(FirestoreService $realtimeDatabaseService)
    {
        $this->realtimeDatabaseService = $realtimeDatabaseService;
    }

    public function index(Request $request)
    {

        try {
            $user = $request->session()->get('user');

            // Récupération des données de la référence "user"
            $stat = $this->realtimeDatabaseService->getAllStats() ?? [];
            //$totalUser = $this->realtimeDatabaseService->getClientsCount();

            //dd($totalUser);

            // Passage des commandes à la vue "clients.index"
            return view('dashboard.accueil', ['stat' => $stat,
        'users'=> $user
        ]);
        } catch (Exception $e) {
            return view('dashboard.accueil')->with(['error' => $e->getMessage()]);
        }
    }


    public function stat()
    {
        // Récupérer toutes les commandes depuis la base de données ou service
        $commandes = $this->realtimeDatabaseService->getData('commandes');

        // Initialiser un tableau pour stocker les statistiques par mois et statut
        $stats = [
            'Picked' => array_fill(0, 12, 0),   // Pour chaque mois (0 à 11)
            'New' => array_fill(0, 12, 0),
            'Paid' => array_fill(0, 12, 0),
            'Traitement' => array_fill(0, 12, 0),
            'Completed' => array_fill(0, 12, 0),
        ];

        // Parcourir toutes les commandes pour les regrouper par mois et statut
        foreach ($commandes as $commande) {

            // Vérifier que 'dateajout' existe et est de type timestamp ou une date valide
            if (isset($commande['dateajout'])) {
                $date = (new \DateTime())->setTimestamp($commande['dateajout'] / 1000);  // Diviser par 1000 si c'est en millisecondes
                $month = $date->format('n') - 1; // Format mois, de 1 à 12, donc -1 pour obtenir 0 à 11

                // Vérifier que le statut est l'un de ceux que nous voulons traiter
                if (in_array($commande['statut'], ['Picked', 'New', 'Paid', 'Traitement','Completed'])) {
                    $stats[$commande['statut']][$month]++;
                }
            }
        }


        // Retourner les statistiques sous forme de JSON
        return response()->json($stats);
    }


}
