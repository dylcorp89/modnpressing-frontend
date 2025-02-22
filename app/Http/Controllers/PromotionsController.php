<?php

namespace App\Http\Controllers;

use App\Services\FirestoreService;
use Illuminate\Http\Request;

class PromotionsController extends Controller
{
    protected $firestoreService;

    public function __construct(FirestoreService $firestoreService)
    {
        $this->firestoreService = $firestoreService;
    }

    /**
     * Afficher la liste des promotions.
     */

    public function index()
{
    try {
        $promotions = $this->firestoreService->getPromotions();

        // Ajouter les clés comme 'id' à chaque promotion
        $promotionsWithIds = [];
        if ($promotions) {
            foreach ($promotions as $key => $promotion) {
                $promotion['id'] = $key;
                $promotionsWithIds[] = $promotion;
            }
        }

        return view('dashboard.promotions', ['promotions' => $promotionsWithIds]);
    } catch (\Exception $e) {
        return back()->withErrors('Erreur lors de la récupération des promotions : ' . $e->getMessage());
    }
}


    /**
     * Ajouter une promotion.
     */
    public function store(Request $request)
    {
        // Validation des données du formulaire
        $validatedData = $request->validate([
            'titre' => 'required|string|max:255',
            'image' => 'required|file|image|max:2048', // Valider le fichier comme une image
            'date_debut' => 'required|date',
            'date_fin' => 'required|date|after_or_equal:date_debut',
        ]);

        try {
            // Gestion de l'upload de l'image
            $imagePath = $request->file('image')->getRealPath();
            $imageName = $request->file('image')->getClientOriginalName();
 // Define the folder path for promotions
 $folderPath = 'promotions/';
            $imageUrl = $this->firestoreService->uploadImageToStorage($imagePath, $imageName);

            // Préparation des données pour Firebase
            $promotionData = [
                'titre' => $validatedData['titre'],
                'image' => $imageUrl,
                'date_debut' => $validatedData['date_debut'],
                'date_fin' => $validatedData['date_fin'],
                'created_at' => now()->toDateTimeString(),
            ];

            // Ajout des données à Firebase
            $this->firestoreService->addPromotion($promotionData);

            return redirect()->back()->with('success', 'Promotion ajoutée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors('Erreur lors de l’ajout de la promotion : ' . $e->getMessage());
        }
    }

    /**
     * Supprimer une promotion.
     */
    public function destroy($id)
    {
        try {
            $this->firestoreService->deleteData('promotions', $id);
            return redirect()->back()->with('success', 'Promotion supprimée avec succès !');
        } catch (\Exception $e) {
            return back()->withErrors('Erreur lors de la suppression de la promotion : ' . $e->getMessage());
        }
    }
}
