@extends('app')

@section('content')

<div class="flex items-center justify-between my-2">
    <!-- Bouton de retour -->
    <a href="{{ route('user-show') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
            stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7 7-7"></path>
        </svg>
        Retour
    </a>



    <div class="flex items-center justify-between my-6">


</div>
</div>

<h2 class="my-2 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    Formulaire utilisateur
</h2>

<div class="container mx-auto px-4 bg-white p-4 min-w-0 rounded-lg shadow-xs dark:bg-gray-800">
    <div class="card-body">

        <!-- Check if the session has a success message -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Check if we are editing an existing user -->
        <form action="{{ isset($user) ? route('update_user',  $id) : route('add_user') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT') <!-- Use PUT for updating -->
            @endif

            <div class="grid-cols-2 flex gap-6 max-w-full ">



            <div class="w-1/2">  <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Nom</span>

                <input name="nom" type="text" class="block w-full pr-10 mt-1 text-sm text-black
                 dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                  placeholder="" value="{{ old('nom', $user['nom'] ?? '') }}" required>
            </label></div>

        <div class="w-1/2">  <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Prénoms</span>
                <input name="prenoms" type="text" class="block w-full pr-10 mt-1 text-sm text-black
                 dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" placeholder=""
                  value="{{ old('prenoms', $user['prenoms'] ?? '') }}" required>
            </label></div>
        </div>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">E-mail</span>
                <input name="email" type="email" class="block w-full pr-10 mt-1 text-sm
                 text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input"
                 placeholder="jane.doe@example.com" value="{{ old('email', $user['email']?? '') }}" required>
            </label>

            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Mot de passe</span>
                <input name="password" type="password" value="{{ old('password', $user['password']?? '') }}" class="block w-full pr-10 mt-1 text-sm text-black
                 dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input" placeholder="********"
                 @if(!isset($user)) required @endif>
            </label>

            <!-- Role Selection -->
            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Rôle</span>
                <select name="role" class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input">
                    <option value="Administrateur" @if(isset($user) && $user['role'] == 'Administrateur') selected @endif>Administrateur</option>
                    <option value="Utilisateur" @if(isset($user) && $user['role'] == 'Utilisateur') selected @endif>Utilisateur</option>
                    <option value="Gerant" @if(isset($user) && $user['role'] == 'Gerant') selected @endif>Gérant</option>
                </select>
            </label>

            <!-- Status Selection -->
            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Statut</span>
                <select name="status" class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 form-input">
                    <option value="actif" @if(isset($user) &&  $user['status'] == 'actif') selected @endif>Actif</option>
                    <option value="inactif" @if(isset($user) &&  $user['status'] == 'inactif') selected @endif>Inactif</option>
                </select>
            </label>

            <button type="submit" class="my-6 px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple">
                {{ isset($user) ? 'Mettre à jour' : 'Ajouter' }}
            </button>
        </form>

    </div>
</div>
@endsection
