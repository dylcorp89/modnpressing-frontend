@extends('app')

@section('content')
<h2
class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
>
Gestion des évaluations
</h2>

<!-- New Table -->
<div class="w-full overflow-hidden rounded-lg shadow-xs">
    <div class="w-full overflow-x-auto">
        <!-- Header Section -->


        <!-- Table Section -->
        <table class="w-full whitespace-no-wrap">
            <thead>
                <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">

                    <th class="px-4 py-3">Nom utilisateur</th>
                    <th class="px-4 py-3">Note</th>
                    <th class="px-4 py-3">Statut</th>
                    <th class="px-4 py-3">Commentaire</th>
                    <th class="px-4 py-3">Action</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                {{-- Loop through users --}}
                @foreach ($claims as $claim)

                <tr class="text-gray-700 dark:text-gray-400">
                    <td class="px-4 py-3">

                          <p class="font-semibold">{{ $claim['nomUtilisateur'] }}</p>

                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $claim['note'] }}
                    </td>
                    <td class="px-4 py-3 text-xs">



                        <span
                        class="px-2 py-1 font-semibold leading-tight rounded-full text-sm
                          {{ $claim['statut'] === 'Approuve' ? 'text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100' : '' }}
                          {{ $claim['statut'] === 'Non approuve' ? 'text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100' : '' }}">
                        {{ $claim['statut'] }}
                      </span>


                    </td>
                    <td class="px-4 py-3 text-sm">

<span> {{ $claim['commentaire'] }}</span>

                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-4 text-sm">


                        <form action="{{ route('apply') }}" method="POST">
                            @csrf
                            <input type="hidden" name="evaluation_id" value="{{ $claim['id'] }}">
                            <input type="hidden" name="evaluation_statut" value="Approuve">
                            <button type="submit" class="block w-full px-4 py-2
                            text-sm font-medium leading-5 text-center
           text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
            active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                                Approuver
                            </button>
                        </form>
                      </div>
                    </td>
                  </tr>

                @endforeach
                {{-- If no users exist --}}
                @if($claims="")
                <tr>
                    <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                        Aucune évalution trouvée.
                    </td>
                </tr>
                @endif
            </tbody>
        </table>
    </div>

    <!-- Pagination Section -->
    <div class="grid px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t dark:border-gray-700 bg-gray-50 sm:grid-cols-9 dark:text-gray-400 dark:bg-gray-800">

        <span class="flex items-center col-span-3">
            @if ($currentPage > 1)
            <a href="?page={{ $currentPage - 1 }}">Précédent</a>
        @endif
        &nbsp;   &nbsp;
        Page {{ $currentPage }} sur {{ $totalPages }}
        </span>
        <span class="col-span-2">  </span>
        <!-- Pagination Links -->
        <span class="flex col-span-4 mt-2 sm:mt-auto sm:justify-end">
            @if ($currentPage < $totalPages)
            <a href="?page={{ $currentPage + 1 }}">Suivant</a>
        @endif
        </span>
    </div>
</div>



@endsection
