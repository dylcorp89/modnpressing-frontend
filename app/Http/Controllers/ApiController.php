<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\FirestoreService;

class ApiController extends Controller
{
    //
    protected $realtimeDatabaseService;
    //
    public function __construct(FirestoreService $realtimeDatabaseService)
    {
        $this->realtimeDatabaseService = $realtimeDatabaseService;
    }


    public function index(){

            // Récupération des données de la référence "user"
            $apis = $this->realtimeDatabaseService->getData('api');

        
            // Passage des commandes à la vue "clients.index"
            return view('dashboard.parametres',['apis'=>$apis] );


    }

    public function add(Request $request)  {

        $validatedData = $request->validate([
            'google_api' => 'nullable|string',
            'admod_api' => 'nullable|string',
        ]);


        $apiData = [
            'google_api' => $validatedData['google_api'],
            'admod_api' => $validatedData['admod_api'],

        ];



     $this->realtimeDatabaseService->addData('api', $apiData);

     //Alert::success('Success Title', 'Success Message');

      return  redirect()->route('api-show')->with('success', 'Succès de l\'opération');
    }

 // Modifier les données des APIs
 public function edit(Request $request, $id)
 {
   // Validate incoming data
$validated = $request->validate([
    'google_api' => 'nullable|string', // Optional string for Google API
    'admod_api' => 'nullable|string',  // Optional string for Admod API
]);

// Prepare the data to be updated
$apis = [];  // Initialize the array to store the API values

// Only add the values to $apis if they are provided
if ($validated['google_api'] !== null) {
    $apis['google_api'] = $validated['google_api'];
}

if ($validated['admod_api'] !== null) {
    $apis['admod_api'] = $validated['admod_api'];
}

// Ensure you have a valid ID before attempting to update
if (isset($id) && !empty($apis)) {
    // Save data to Firebase Realtime Database
    $this->realtimeDatabaseService->updateData('api', $apis, $id);
} else {
    // Handle the case where the ID or APIs are not valid
    return response()->json(['error' => 'Invalid data or ID'], 400);
}

     // Redirection avec un message de succès
     return redirect()->back()->with('success', 'Les API ont été mises à jour avec succès.');
 }






}
