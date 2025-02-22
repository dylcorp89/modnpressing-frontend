@extends('app')

@section('content')

<h2
class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
>
Liste des clients
</h2>
 <!-- New Table -->
 <div class="w-full overflow-hidden rounded-lg shadow-xs">
    <div class="w-full overflow-x-auto">
      <table class="w-full whitespace-no-wrap">
        <thead>
          <tr
            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800"
          >
            <th class="px-4 py-3"></th>
            <th class="px-4 py-3">Email</th>
            <th class="px-4 py-3">Dernière connexion</th>
            <th class="px-4 py-3">Date de création</th>
          </tr>
        </thead>
        <tbody
          class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800"
        >

         @foreach ($users as $customer)
                {{-- <tr class="text-gray-700 dark:text-gray-400">
                    <td>{{ $customer->uid ?? 'N/A' }}</td>
                    <td>{{ $customer->name ?? 'N/A' }}</td>
                    <td>{{ $customer->email ?? 'N/A' }}</td>


                </tr> --}}
                <tr class="text-gray-700 dark:text-gray-400">
                    <td class="px-4 py-3">
                      <div class="flex items-center text-sm">
                        <!-- Avatar with inset shadow -->
                        <div
                          class="relative hidden w-8 h-8 mr-3 rounded-full md:block"
                        >
                          <img
                            class="object-cover w-full h-full rounded-full"
                            src="{{ $customer->photoUrl ?? '#' }}"
                            alt=""
                            loading="lazy"
                          />
                          <div
                            class="absolute inset-0 rounded-full shadow-inner"
                            aria-hidden="true"
                          ></div>
                        </div>
                        <div>
                          <p class="font-semibold">{{ $customer->displayName ?? 'N/A' }}</p>
                          <p class="text-xs text-gray-600 dark:text-gray-400">

                            Email Vérifié : <span
  class="px-2 py-1 font-semibold leading-tight rounded-full
         {{ $customer->disabled ? 'text-red-700 bg-red-100 dark:text-red-100 dark:bg-red-700' : 'text-green-700 bg-green-100 dark:text-green-100 dark:bg-green-700' }}"
>
  {{ $customer->disabled ? 'Non' : 'Oui' }}
</span>

                          </p>
                        </div>
                      </div>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $customer->email ?? 'N/A' }}
                    </td>
                    <td class="px-4 py-3 text-xs">
                      <span
                        class="px-2 py-1 font-semibold leading-tight text-gray-700 bg-orange-100 rounded-full dark:text-gray-100 dark:bg-orange-700"
                      >
                      {{ $customer->metadata->lastLoginAt?->format('Y-m-d H:i:s') ?? 'N/A' }}
                      </span>
                    </td>
                    <td class="px-4 py-3 text-sm">
                        {{ $customer->metadata->createdAt?->format('Y-m-d H:i:s') ?? 'N/A' }}

                    </td>
                  </tr>
            @endforeach








        </tbody>
      </table>
    </div>
    <div
      class="grid px-4 py-3 text-xs font-semibold tracking-wide text-gray-500 uppercase border-t dark:border-gray-700 bg-gray-50 sm:grid-cols-9 dark:text-gray-400 dark:bg-gray-800"
    >

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
