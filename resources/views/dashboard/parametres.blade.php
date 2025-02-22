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

</div>
<h2 class="my-2 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    Gestion des API
</h2>

<div class="bg-white dark:bg-gray-800 shadow-md rounded-lg p-6">
    <div class="card-body">
        <!-- Notification de succès -->
        @if (session('success'))
        <div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
            {{ session('success') }}
        </div>
        @endif

        <!-- Notification d'erreurs -->
        @if ($errors->any())
        <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-400 rounded">
            <ul>
                @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <form action="{{ route('edit-api' , ['id' => array_key_first($apis)] ) }} " method="POST">
            @csrf


@foreach ($apis as $api )



            <!-- Champ Google API -->
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-400">
                <span class="text-gray-700 dark:text-gray-400">Google Api key</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="google_api"
                        value="{{ $api['google_api'] ?? '' }}"
                        class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                        placeholder="Votre Google API Key"
                    />
                </div>
            </label>

            <!-- Champ Admob API -->
            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Admob Api key</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="admod_api"
                        value="{{ $api['admod_api'] ?? '' }}"
                        class="mt-1 block w-full text-sm text-black dark:text-gray-300 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 rounded-lg p-2 focus:ring focus:ring-blue-300"
                        placeholder="Votre Admob API Key"
                    />
                </div>
            </label>
            @endforeach
            <!-- Bouton d'enregistrement -->
            <button
                type="submit"
                class="px-6 py-2 my-4 text-white font-medium bg-blue-600 hover:bg-blue-700 rounded-lg transition duration-150"
            >
                Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection
