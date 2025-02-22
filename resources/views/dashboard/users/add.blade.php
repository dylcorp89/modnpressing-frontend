@extends('app')

@section('content')

    <!-- Bouton de retour -->
    <div class="flex items-center justify-between mb-6">
        <a href="{{ route('user-show') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7 7-7"></path>
            </svg>
            Retour
        </a>
    </div>

    <!-- Titre -->
    <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200 mb-4">
        Formulaire utilisateur
    </h2>

    <!-- Carte contenant le formulaire -->
    <div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">

        <!-- Message de succès -->
        @if (session('success'))
            <div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- Formulaire utilisateur -->
        <form action="{{ isset($user) ? route('update_user', $id) : route('add_user') }}" method="POST">
            @csrf
            @if(isset($user))
                @method('PUT')
            @endif

            <div class="grid grid-cols-2 gap-6">
                <!-- Nom -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Nom</label>
                    <input name="nom" type="text" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                        value="{{ old('nom', $user['nom'] ?? '') }}" required>
                </div>

                <!-- Prénoms -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Prénoms</label>
                    <input name="prenoms" type="text" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                        value="{{ old('prenoms', $user['prenoms'] ?? '') }}" required>
                </div>
            </div>

            <!-- Email -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">E-mail</label>
                <input name="email" type="email" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                    placeholder="jane.doe@example.com" value="{{ old('email', $user['email'] ?? '') }}" required>
            </div>

            <!-- Mot de passe -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Mot de passe</label>
                <input name="password" type="password" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                    placeholder="********" @if(!isset($user)) required @endif>
            </div>

            <!-- Sélection du rôle -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Rôle</label>
                <select name="role" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300">
                    <option value="Administrateur" @if(isset($user) && $user['role'] == 'Administrateur') selected @endif>Administrateur</option>
                    <option value="Utilisateur" @if(isset($user) && $user['role'] == 'Utilisateur') selected @endif>Utilisateur</option>
                    <option value="Gerant" @if(isset($user) && $user['role'] == 'Gerant') selected @endif>Gérant</option>
                </select>
            </div>

            <!-- Sélection du statut -->
            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">Statut</label>
                <select name="status" class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300">
                    <option value="actif" @if(isset($user) && $user['status'] == 'actif') selected @endif>Actif</option>
                    <option value="inactif" @if(isset($user) && $user['status'] == 'inactif') selected @endif>Inactif</option>
                </select>
            </div>

            <!-- Bouton de soumission -->
            <div class="mt-6 flex justify-end">
                <button type="submit" class="px-6 py-2 text-white font-medium bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-150">
                    {{ isset($user) ? 'Mettre à jour' : 'Ajouter' }}
                </button>
            </div>
        </form>
    </div>


@endsection
