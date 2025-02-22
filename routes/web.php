<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CaisseController;
use App\Http\Controllers\AccueilController;
use App\Http\Controllers\CampagneController;
use App\Http\Controllers\ClientsController;
use App\Http\Controllers\CommandesController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\PromotionsController;
use App\Http\Controllers\ReclamationsController;
use App\Http\Controllers\UtilisateursController;
use App\Http\Controllers\TarificationsController;

/*
|--------------------------------------------------------------------------|
| Web Routes                                                              |
|--------------------------------------------------------------------------|
| Here is where you can register web routes for your application. These   |
| routes are loaded by the RouteServiceProvider and all of them will       |
| be assigned to the "web" middleware group. Make something great!         |
*/

Route::get('/', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'authenticate'])->name('login_verif');

// Routes protégées par le middleware 'check.user.status'
//Route::group(['middleware' => ['check.user.status']], function () {

    // Routes principales
    Route::get('/home', [AccueilController::class, 'index'])->name('home');
    Route::get('/stat', [AccueilController::class, 'stat'])->name('stat');

    // Routes Commandes
    Route::prefix('commandes')->group(function () {
        Route::get('/', [CommandesController::class, 'index'])->name('commandes');
        Route::get('/details/{id}', [CommandesController::class, 'details'])->name('details');
        Route::get('/detail/{id}', [CommandesController::class, 'details2'])->name('details2');
        Route::post('/apply-points', [CommandesController::class, 'applyPoints'])->name('applyPoints');
        Route::post('/prix/{id}', [CommandesController::class, 'prix'])->name('prix');
        Route::post('/traitrement/{id}', [CommandesController::class, 'traitement'])->name('traitement');
    });

    // Route Clients
    Route::get('/clients', [ClientsController::class, 'index'])->name('clients');

    // Routes Promotions
    Route::prefix('promotions')->group(function () {
        Route::get('/', [PromotionsController::class, 'index'])->name('promotions');
        Route::post('/add-promotion', [PromotionsController::class, 'store'])->name('add-promotion');
        Route::delete('delete/{id}', [PromotionsController::class, 'destroy'])->name('destroy');
    });

    // Routes Campagnes
    Route::prefix('campagnes')->group(function () {
        Route::get('/', [CampagneController::class, 'index'])->name('campagnes');
        Route::post('/add-campagne', [CampagneController::class, 'store'])->name('add-campagne');
        Route::delete('delete/{id}', [CampagneController::class, 'destroy'])->name('delete-campagne');
    });

    // Route Caisse
    Route::get('/caisse', [CaisseController::class, 'index'])->name('caisse');

    // Route Evaluation
    Route::get('/evaluation', [EvaluationController::class, 'index'])->name('evaluation');
    Route::post('/apply', [EvaluationController::class, 'apply'])->name('apply');

    // Route Reclamations
    Route::get('/reclamations', [ReclamationsController::class, 'index'])->name('reclamations');

    // Route Tarifications
    Route::get('/tarifications', [TarificationsController::class, 'index'])->name('tarifications');

    // Routes Utilisateurs
    Route::prefix('users')->group(function () {
        Route::get('/show', [UtilisateursController::class, 'index'])->name('user-show');
        Route::get('/add', [UtilisateursController::class, 'add'])->name('add-user');
        Route::post('/add_user', [UtilisateursController::class, 'add_user'])->name('add_user');
        Route::get('/edit/{id}', [UtilisateursController::class, 'edit'])->name('edit_user');
        Route::put('/update/{id}', [UtilisateursController::class, 'update'])->name('update_user');
        Route::delete('{id}/delete/', [UtilisateursController::class, 'delete'])->name('delete-user');
    });

    // // Route Logout
    Route::post('/logout', function () {
        Auth::logout(); // Déconnecter l'utilisateur
        session()->invalidate(); // Invalider la session
        session()->regenerateToken(); // Regénérer le token CSRF
        return redirect('/'); // Rediriger vers la page d'accueil
    })->name('logout');

    // Routes API
    Route::prefix('api')->group(function () {
        Route::get('/show', [ApiController::class, 'index'])->name('api-show');
        Route::post('/add', [ApiController::class, 'add'])->name('add-api');
        Route::post('{id}/edit', [ApiController::class, 'edit'])->name('edit-api');
        Route::delete('{id}/delete/', [ApiController::class, 'delete'])->name('delete');
    });

//});

