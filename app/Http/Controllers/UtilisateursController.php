<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Services\FirestoreService;
use Illuminate\Support\Facades\Hash;
use Illuminate\Pagination\LengthAwarePaginator;
use Kreait\Firebase\RemoteConfig\User;
use RealRashid\SweetAlert\Facades\Alert;


class UtilisateursController extends Controller
{
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
            $users = $this->realtimeDatabaseService->getData('utilisateurs') ?? [];

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


            return view('dashboard.utilisateurs', [

                'users' => $paginatedData,
                'currentPage' => $page,
                'totalPages' => $totalPages,
            ]);
        } catch (Exception $e) {
            return view('clients.index')->with(['error' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {

        $user = $this->realtimeDatabaseService->getAllFieldsById('utilisateurs', $id);

        return view('dashboard.users.add', [
            "user" => $user,
            "id" => $id,
        ]);
    }

    public function add()
    {
        return view('dashboard.users.add');
    }

    public function add_user(Request $request)
    {

        $validatedData = $request->validate([
            'nom' => 'required|string|max:255',
            'prenoms' => 'required|string|max:255',
            'email' => 'required|email',
            'password' => 'required|string|min:8', // Use `confirmed` if you have a password confirmation field
            'role' => 'required|string',
            'status' => 'required|string',
        ]);

        $userData = [
            'nom' => $validatedData['nom'],
            'prenoms' => $validatedData['prenoms'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']), // You should hash the password before saving
            'role' => $validatedData['role'],
            'status' => $validatedData['status'],
        ];

        // dd($this->realtimeDatabaseService->addData('utilisateurs', $userData));
        $this->realtimeDatabaseService->addData('utilisateurs', $userData);

        //Alert::success('Success Title', 'Success Message');

        return  redirect()->route('add-user')->with('success', 'Utilisateur ajouté avec succès');
    }

    public function update(Request $request, $id)
    {
        //dd($request);
        $validated = $request->validate([

            'nom' => 'nullable|string',

            'prenoms' => 'nullable|string',

            'password' => 'nullable|string|min:8',
            'role' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        $user = $this->realtimeDatabaseService->updateData('utilisateurs', $validated, $id);


        return redirect()->back()->with('success', 'Utilisateur mis à jour avec succès!');
    }




    public function delete(Request $request, $id)
    {
        // Delete the user data with the given ID from Firebase
        $this->realtimeDatabaseService->deleteData('utilisateurs', $id);

        // alert()->success('SuccessAlert','Lorem ipsum dolor sit amet.');

        // Redirect back to the user list page with a success message
        return redirect()->route('user-show')->with('success', 'Utilisateur supprimé avec succès');
    }
}
