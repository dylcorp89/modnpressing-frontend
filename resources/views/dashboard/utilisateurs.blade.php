@extends('app')

@section('content')
    <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
        Gestion des utilisateurs
    </h2>

    <!-- New Table -->
    <div class="w-full overflow-hidden rounded-lg shadow-xs">
        <div class="w-full overflow-x-auto">
            <!-- Header Section -->
            <div class="flex items-center justify-between p-4 mb-8  text-sm  font-semibold text-purple-100 bg-purple-600 rounded-lg shadow-md focus:outline-none focus:shadow-outline-purple">
                <span class="font-semibold text-white dark:text-gray-200">
                    Liste des utilisateurs
                </span>
                <button
                    class="px-4 py-2 text-sm font-medium text-purple-600 bg-white border border-purple-600 rounded-lg shadow hover:bg-purple-600 hover:text-white focus:outline-none focus:shadow-outline-purple"
                    onclick="window.location.href='{{ route('add-user') }}'"
                >
                    Ajouter un Utilisateur
                </button>
            </div>
            <!-- Check if the session has a success message -->
            @if (session('success'))
                <div class="mb-4 p-4 bg-red-100 text-red-800 border border-red-400 rounded">
                    {{ session('success') }}
                </div>
            @endif

 <!-- Table Section -->
 <table class="w-full whitespace-no-wrap">
    <thead>
        <tr class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">

            <th class="px-4 py-3">Nom</th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Statut</th>
            <th class="px-4 py-3">Date</th>
            <th class="px-4 py-3">Action</th>
        </tr>
    </thead>
    <tbody class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
        {{-- Loop through users --}}
        @foreach ($users as $user)

        <tr class="text-gray-700 dark:text-gray-400">
            <td class="px-4 py-3">
              <div class="flex items-center text-sm">
                <!-- Avatar with inset shadow -->
                <div
                  class="relative hidden w-8 h-8 mr-3 rounded-full md:block"
                >
                  <img
                    class="object-cover w-full h-full rounded-full"
                    src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?ixlib=rb-1.2.1&q=80&fm=jpg&crop=entropy&cs=tinysrgb&w=200&fit=max&ixid=eyJhcHBfaWQiOjE3Nzg0fQ"
                    alt=""
                    loading="lazy"
                  />
                  <div
                    class="absolute inset-0 rounded-full shadow-inner"
                    aria-hidden="true"
                  ></div>
                </div>
                <div>
                  <p class="font-semibold">{{ $user['nom'] }}</p>
                  <p class="text-xs text-gray-600 dark:text-gray-400">
                    {{  $user['prenoms']  }}
                  </p>
                </div>
              </div>
            </td>
            <td class="px-4 py-3 text-sm">
                {{  $user['email']}}
            </td>
            <td class="px-4 py-3 text-xs">
              <span
                class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100"
              >
              {{  $user['status']}}

              </span>
            </td>
            <td class="px-4 py-3 text-sm">
                {{-- {{ $user->created_at->format('d/m/Y') }} --}}
            </td>
            <td class="px-4 py-3">
              <div class="flex items-center space-x-4 text-sm">
               <a href="{{ route('edit_user', ['id' => $user['id']]) }}"><button
                  class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray"
                  aria-label="Edit"
                >
                  <svg
                    class="w-5 h-5"
                    aria-hidden="true"
                    fill="currentColor"
                    viewBox="0 0 20 20"
                  >
                    <path
                      d="M13.586 3.586a2 2 0 112.828 2.828l-.793.793-2.828-2.828.793-.793zM11.379 5.793L3 14.172V17h2.828l8.38-8.379-2.83-2.828z"
                    ></path>
                  </svg>
                </button></a>
                <form action="{{ route('delete-user', ['id' => $user['id']]) }}" method="POST" onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="flex items-center justify-between px-2 py-2 text-sm font-medium leading-5 text-purple-600 rounded-lg dark:text-gray-400 focus:outline-none focus:shadow-outline-gray" aria-label="Delete">
                        <svg class="w-5 h-5" aria-hidden="true" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                        </svg>
                    </button>
                </form>


              </div>
            </td>
          </tr>

        @endforeach
        {{-- If no users exist --}}
        @if($users =="")
        <tr>
            <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                Aucun utilisateur trouvé.
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
