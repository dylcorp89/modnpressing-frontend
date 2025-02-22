@extends('app')

@section('content')
<h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
    Gestion des promotions
</h2>

<!-- Afficher les messages de succès -->
@if (session('success'))
<div class="mb-4 p-4 bg-green-100 text-green-800 border border-green-400 rounded">
    {{ session('success') }}
</div>
@endif
@if ($errors->any())
    <div class="alert alert-danger">
        <ul>
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif


<!-- Formulaire pour ajouter une promotion -->
<div class="p-6 mb-8 bg-white rounded-lg shadow-md dark:bg-gray-800">
    <form action="{{ route('add-promotion') }}" method="POST" enctype="multipart/form-data">
        @csrf
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
            <div>
                <label class="block text-sm" for="titre">Titre de la promotion</label>
                <input type="text" name="titre" id="titre" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring" required>
            </div>
            <div>
                <label class="block text-sm" for="image">Image</label>
                <input type="file" name="image" id="image" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring" required>
            </div>
            <div class=" grid-cols-2 flex  mt-10">
            <div>
                <label class="block text-sm" for="date_debut">Date de début</label>
                <input type="date" name="date_debut" id="date_debut" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring" required>
            </div>
           <div class=" w-7">
            &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
           </div>
            <div>
                <label class="block text-sm" for="date_fin">Date de fin</label>
                <input type="date" name="date_fin" id="date_fin" class="w-full px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring" required>
            </div>
            </div>
        </div>
        <button type="submit" class="mt-4 px-4 py-2 text-white bg-purple-600 rounded-lg hover:bg-purple-700">
            Ajouter la promotion
        </button>
    </form>
</div>

<!-- Liste des promotions -->
<div class="w-full overflow-hidden rounded-lg shadow-xs">
    <div class="w-full overflow-x-auto">
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b bg-gray-50 dark:border-gray-700 dark:bg-gray-800 dark:text-gray-400">
                    <th class="px-4 py-3">Titre</th>
                    <th class="px-4 py-3">Image</th>
                    <th class="px-4 py-3">Date de début</th>
                    <th class="px-4 py-3">Date de fin</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">

                @forelse ($promotions as $promotion)
                <tr class="text-gray-700 dark:text-gray-400">
                    <td class="px-4 py-3">{{ $promotion['titre'] }}</td>
                    <td class="px-4 py-3">
                        <img src="{{ $promotion['image'] }}" alt="Image" class="w-12 h-12 rounded">
                    </td>
                    <td class="px-4 py-3">{{ $promotion['date_debut'] }}</td>
                    <td class="px-4 py-3">{{ $promotion['date_fin'] }}</td>
                    <td class="px-4 py-3">
                        <div class="flex items-center space-x-4">
                            {{-- ['id' => $promotion['id']] <a href="{{ route('promotions.edit', $promotion['id']) }}" class="text-purple-600 hover:text-purple-900">Modifier</a> --}}
                            <form action="{{ route('destroy', ['id' => $promotion['id']]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cette promotion ?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-red-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                                    <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                    </svg>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">
                        Aucune promotion disponible.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
