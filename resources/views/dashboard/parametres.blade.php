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

<div class="container mx-auto px-4 bg-white p-4 my-2 min-w-0 rounded-lg shadow-xs dark:bg-gray-800">
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
            <label class="block mt-4 text-sm">
                <span class="text-gray-700 dark:text-gray-400">Google Api key</span>
                <div class="relative text-gray-500 focus-within:text-purple-600 dark:focus-within:text-purple-400">
                    <input
                        name="google_api"
                        value="{{ $api['google_api'] ?? '' }}"
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
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
                        class="block w-full pr-10 mt-1 text-sm text-black dark:text-gray-300 dark:border-gray-600 dark:bg-gray-700 focus:border-purple-400 focus:outline-none focus:shadow-outline-purple dark:focus:shadow-outline-gray form-input"
                        placeholder="Votre Admob API Key"
                    />
                </div>
            </label>
            @endforeach
            <!-- Bouton d'enregistrement -->
            <button
                type="submit"
                class="my-6 px-4 py-2 text-sm font-medium leading-5 text-white transition-colors duration-150 bg-purple-600 border border-transparent rounded-lg active:bg-purple-600 hover:bg-purple-700 focus:outline-none focus:shadow-outline-purple"
            >
                Enregistrer
            </button>
        </form>
    </div>
</div>
@endsection
