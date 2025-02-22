@extends('app')

@section('content')
    <div class="flex items-center justify-between my-6">
        <!-- Bouton de retour -->
        <a href="{{ route('commandes') }}" class="text-blue-600 hover:text-blue-800 font-medium flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 mr-2" fill="none" viewBox="0 0 24 24"
                stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 12H5"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l-7-7 7-7"></path>
            </svg>
            Retour
        </a>



        <div class="flex items-center justify-between my-6">

            <!-- Titre -->
            <h2 class="text-2xl font-semibold text-gray-700 dark:text-gray-200">
                Détails de la commande
            </h2>
            &nbsp;

            <span
                class="px-2 py-1 font-semibold leading-tight rounded-full text-sm
            @if ($commandes['statut'] == 'New') text-orange-700 bg-orange-100 dark:bg-orange-700 dark:text-orange-100

            @elseif ($commandes['statut'] == 'Completed')text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100
   @elseif ($commandes['statut'] == 'Paid') text-green-700 bg-green-100 dark:bg-green-700 dark:text-green-100

            @elseif($commandes['statut'] == 'Traitement') text-blue-700 bg-blue-100 dark:bg-blue-700 dark:text-blue-100

                  @elseif($commandes['statut'] == 'Picked') text-orange-800 bg-orange-100 dark:bg-orange-800 dark:text-orange-200

            @elseif($commandes['statut'] == 'Cancelled') text-red-700 bg-red-100 dark:bg-red-700 dark:text-red-100 @endif">

                {{ $commandes['statut'] }}
            </span>

        </div>
    </div>


    <div class="flex bg-white-100">
        <!-- Première colonne -->

        <div class="w-1/2 p-4 ">
            <div class="bg-white p-4">
                {{-- <h2 class="text-lg font-semibold">Informations du  client</h2> --}}
                <p class="text-gray-600 ">Date collecte : <b class="text-orange-500 text-2xl">
                        {{ strtotime($commandes['collecteDate'] ?? '')
                            ? \Carbon\Carbon::parse($commandes['collecteDate'])->format('d-m-Y')
                            : 'Non spécifiée' }}</b>
                </p>
                <p class="text-gray-600 ">Crénau horaire : <b class=" text-orange-500 text-2xl">
                        {{ $commandes['collecteCreneau'] ?? 'Non spécifiée' }}</b></p>
            </div>
            <div class="bg-white p-4 mb-4  ">
                <h2 class="text-lg font-semibold">Tarification</h2>
                @php
                 $isPaid = $commandes['statut'] == 'Paid';

                $isCompleted = $commandes['statut'] == 'Completed';

                $isPicked = $commandes['statut'] == 'Picked';

                $isTraitement = $commandes['statut'] == 'Traitement';

                @endphp

                <!-- Formulaire pour collecter -->
                @if (!$isPicked && !$isTraitement && !$isCompleted && !$isPaid)
                    <form action="{{ route('traitement', $id) }}" method="post">
                        @csrf
                        <input type="hidden" name="traitement" value="Picked">
                        <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
                active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                            Collecter
                        </button>
                    </form>
                @endif

                <!-- Formulaire pour entrer le prix -->
                @if ($isPicked && !$isTraitement && !$isCompleted && !$isPaid)
                    <form action="{{ route('prix', $id) }}" method="post">
                        @csrf
                        <div class="flex space-x-3">
                            <div>
                                <label class="block mt-4 text-sm">
                                    <input name="prix"
                                           class="block w-full mt-1 text-sm form-input"
                                           placeholder="25000" type="text" />
                                </label>
                            </div>
                            <div>
                                <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
                active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                                    Enregistrer
                                </button>
                            </div>
                        </div>
                    </form>
                @endif

                <!-- Formulaire pour finaliser et notifier -->
                @if ($isTraitement && !$isCompleted && !$isPaid)
                    <form action="{{ route('traitement', $id) }}" method="post">
                        @csrf
                        <input type="hidden" name="traitement" value="Completed">
                        <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
                active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                         Linge prêt
                        </button>
                    </form>
                @endif

                <!-- Formulaire pour finaliser et notifier -->
                @if ($isCompleted && !$isPaid)
                    <form action="{{ route('traitement', $id) }}" method="post">
                        @csrf
                        <input type="hidden" name="traitement" value="Paid">
                        <button type="submit" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-red-600 border border-transparent rounded-lg
                active:bg-red-600 hover:bg-red-700 focus:outline-none focus:shadow-outline-purple">
                        Paiement reçu
                        </button>
                    </form>
                @endif

                <!-- Affichage du prix si finalisé -->
                @if ($isTraitement || $isCompleted || $isPaid)
                <br>
                    <p class="">
                     <span class="text-orange-500 text-2xl "> {{ number_format($commandes['prix'], 0, ',', ' ') }} F CFA</span>
                    </p>
                @endif


            </div>
            <div class="bg-white p-4">
                <h2 class="text-lg font-semibold space-y-3">Informations du client</h2>

                <div class="space-y-2">
                    <div class="flex justify-between">
                        <span class="text-gray-700 font-medium">Nom :</span>
                        <span class="text-gray-600 uppercase">{{ $user->displayName ?? $userMore['info']['name'] ?? explode("-", $commandes['anotherInfo'])[0] ?? 'Nom inconnu' }}
                        </span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700 font-medium">Téléphone :</span>
                        <span class="text-gray-600">{{ $user->phoneNumber ?? $userMore['info']['telephone'] ?? explode("-", $commandes['anotherInfo'])[1] ?? 'Nom inconnu' }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-700 font-medium">Email :</span>
                        <span class="text-gray-600">{{ $user->email ?? $userMore['info']['email'] ?? 'Utilisateur non connecté' }}</span>
                    </div>


                    <div class="flex justify-between">
                        <span class="text-gray-700 font-medium">Points de fidélité :</span>


                        @if ($userMore['info']['utilisation'] ?? '0'== 'oui')
                            <form action="{{ route('applyPoints') }}" method="POST">
                                @csrf
                                <input type="hidden" name="user_id" value="{{ $user->uid }}">
                                <button type="submit" class="block w-full px-4 py-2
                                text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
                active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                                    Appliquer les points {{ $user->point ?? $userMore['info']['point'] ?? '0' }}
                                </button>
                            </form>
                            @else
                            <span class="text-2xl text-orange-500">{{ $user->point ?? $userMore['info']['point'] ?? '0' }}</span>

                        @endif

                    </div>
                </div>

            </div>
            <div class="bg-white p-4 ">
                <div class="space-y-3">
                    <h2 class="text-lg font-semibold text-gray-700">Informations sur les articles</h2>

                    <div class="flex justify-between mb-4">
                        <span class="text-gray-700 font-medium">Type de la commande :</span>
                        <span class="text-gray-600">{{ $commandes['typecommande'] ?? 'N/A' }}</span>
                    </div>

                    @foreach ($commandes['articles'] as $article)
                        @if ($article['quantite'] != 0)
                            <div class="space-y-3 border-b border-gray-200 pb-4">
                                <div class="flex justify-between">
                                    <span class="text-gray-700 font-medium">Type d'article :</span>
                                    <span class="text-gray-600">{{ $article['articleType'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700 font-medium">Option :</span>
                                    <span class="text-gray-600">{{ $article['option'] ?? 'N/A' }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-gray-700 font-medium">Quantité :</span>
                                    <span class="text-gray-600">{{ $article['quantite'] }}</span>
                                </div>
                            </div>
                        @endif
                    @endforeach
                </div>



            </div>
            <div class="bg-white p-4  ">
                <h2 class="text-lg font-semibold">Informations complémentaires</h2>
                <p class="text-gray-600">{{ $commandes['anotherInfo'] ?? '' }}</p>
            </div>

        </div>

        <!-- Deuxième colonne -->
        <div class="w-1/2 p-4">
            <div class="bg-white p-4 rounded shadow-md">
                <h2 class="text-lg font-semibold">Map</h2>
                <div id="map"></div>
            </div>
            <div class="bg-white p-4 rounded shadow-md">
                <h2 class="text-lg font-semibold">Adresse</h2>
                <p class="text-gray-600">{{ $commandes['collecteAdresse'] ?? '' }}</p>
                <p>
                    <a id="whatsapp-share" href="#" target="_blank" class="block w-full px-4 py-2 mt-4 text-sm font-medium leading-5 text-center
               text-white transition-colors duration-150 bg-blue-600 border border-transparent rounded-lg
                active:bg-blue-600 hover:bg-blue-700 focus:outline-none focus:shadow-outline-purple">
                        Partager la localisation via WhatsApp
                    </a>
                </p>



            </div>
        </div>
    </div>
@endsection

@section('scripts')

<script>
    $(document).ready(function() {
        // Capture la soumission du formulaire
        $('#prixForm').on('submit', function(e) {
            e.preventDefault();  // Empêche le rechargement de la page

            var formData = $(this).serialize();  // Sérialise les données du formulaire

            $.ajax({
                url: $(this).attr('action'),  // URL de l'action du formulaire
                type: 'POST',  // Méthode POST
                data: formData,  // Données à envoyer
                success: function(response) {
                    // Gérer la réponse
                    alert('Formulaire soumis avec succès !');  // Afficher un message de succès
                    // Vous pouvez aussi manipuler la réponse ici, par exemple :
                    // $('#result').html(response);
                },
                error: function(xhr, status, error) {
                    // Gérer les erreurs
                    alert('Erreur lors de la soumission du formulaire.');
                }
            });
        });
    });
</script>
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>

    <script>
        // Les coordonnées passées depuis Laravel
        const latitude = {{ $commandes['collecteLatitude'] ?? '0' }};
        const longitude = {{ $commandes['collecteLongitude'] ?? '0' }};
        const whatsappLink = `https://wa.me/?text=${encodeURIComponent(`Voici la localisation : https://www.google.com/maps?q=${latitude},${longitude}`)}`;

document.getElementById('whatsapp-share').href = whatsappLink;
        // Initialisation de la carte
        const map = L.map('map').setView([latitude, longitude], 13);

        // Ajouter des tuiles OpenStreetMap
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(map);

        // Ajouter un marqueur
        L.marker([latitude, longitude]).addTo(map)
            .bindPopup('Adresse exacte.')
            .openPopup();
    </script>
@endsection
