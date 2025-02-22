@extends('app')

@section('content')
<h2
class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
>
Gestion des reclamations
</h2>

<!-- New Table -->
<div class="w-full overflow-hidden rounded-lg shadow-xs">
    <div class="w-full overflow-x-auto">
        <!-- Header Section -->


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
                @foreach ($claims as $claim)

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
                          <p class="font-semibold">{{ $claim['sujet'] }}</p>
                          <p class="text-xs text-gray-600 dark:text-gray-400">
                            {{ $claim['type'] }}
                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $claim['description'] }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                      <span
                        class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full dark:bg-green-700 dark:text-green-100"
                      >
                      {{-- {{ $claim['status'] }} --}}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ isset($claims['dateajout']) ? \Carbon\Carbon::createFromTimestamp($claims['dateajout'] / 1000)->format('d/m/Y') : '' }}


                    </td>
                    <td class="px-4 py-3">
                      <div class="flex items-center space-x-4 text-sm">
                        <button
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
                        </button>

                        </button>
                      </div>
                    </td>
                  </tr>

                @endforeach
                {{-- If no users exist --}}
                @if($claims="")
                <tr>
                    <td colspan="4" class="px-4 py-3 text-center text-gray-500 dark:text-gray-400">
                        Aucune reclamation trouvée.
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
