@extends('app')

@section('content')

    <h2
      class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200"
    >
      Tableau de bord
    </h2>
    <div class="flex flex-wrap -mx-4">
        <!-- Card -->
        <a href="{{ route('clients') }}" class="w-1/4 px-4 mb-4 md:mb-0">
          <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-orange-500 bg-orange-100 rounded-full dark:text-orange-100 dark:bg-orange-500">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M13 6a3 3 0 11-6 0 3 3 0 016 0zM18 8a2 2 0 11-4 0 2 2 0 014 0zM14 15a4 4 0 00-8 0v3h8v-3zM6 8a2 2 0 11-4 0 2 2 0 014 0zM16 18v-3a5.972 5.972 0 00-.75-2.906A3.005 3.005 0 0119 15v3h-3zM4.75 12.094A5.973 5.973 0 004 15v3H1v-3a3 3 0 013.75-2.906z"></path>
              </svg>
            </div>
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Total clients</p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stat['clients'] }}</p>
            </div>
          </div>
        </a>

        <!-- Card -->
        <div class="w-1/4 px-4 mb-4 md:mb-0">
          <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-green-500 bg-green-100 rounded-full dark:text-green-100 dark:bg-green-500">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M4 4a2 2 0 00-2 2v4a2 2 0 002 2V6h10a2 2 0 00-2-2H4zm2 6a2 2 0 012-2h8a2 2 0 012 2v4a2 2 0 01-2 2H8a2 2 0 01-2-2v-4zm6 4a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Caisse</p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">XOF 0</p>
            </div>
          </div>
        </div>

        <!-- Card -->
        <a href="{{ route('commandes') }}" class="w-1/4 px-4 mb-4 md:mb-0">
          <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-blue-500 bg-blue-100 rounded-full dark:text-blue-100 dark:bg-blue-500">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path d="M3 1a1 1 0 000 2h1.22l.305 1.222a.997.997 0 00.01.042l1.358 5.43-.893.892C3.74 11.846 4.632 14 6.414 14H15a1 1 0 000-2H6.414l1-1H14a1 1 0 00.894-.553l3-6A1 1 0 0017 3H6.28l-.31-1.243A1 1 0 005 1H3zM16 16.5a1.5 1.5 0 11-3 0 1.5 1.5 0 013 0zM6.5 18a1.5 1.5 0 100-3 1.5 1.5 0 000 3z"></path>
              </svg>
            </div>
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Commandes</p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stat['commandes'] }}</p>
            </div>
          </div>
        </a>

        <!-- Card -->
        <a href="{{ route('reclamations') }}" class="w-1/4 px-4">
          <div class="flex items-center p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800">
            <div class="p-3 mr-4 text-teal-500 bg-teal-100 rounded-full dark:text-teal-100 dark:bg-teal-500">
              <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                <path fill-rule="evenodd" d="M18 5v8a2 2 0 01-2 2h-5l-5 4v-4H4a2 2 0 01-2-2V5a2 2 0 012-2h12a2 2 0 012 2zM7 8H5v2h2V8zm2 0h2v2H9V8zm6 0h-2v2h2V8z" clip-rule="evenodd"></path>
              </svg>
            </div>
            <div>
              <p class="mb-2 text-sm font-medium text-gray-600 dark:text-gray-400">Réclamations</p>
              <p class="text-lg font-semibold text-gray-700 dark:text-gray-200">{{ $stat['reclamations'] }}</p>
            </div>
          </div>
        </a>
      </div>




    <!-- Charts -->
    <h2
      class="my-4 text-2xl font-semibold text-gray-700 dark:text-gray-200"
    >
      Statistiques
    </h2>


      <div
        class="min-w-0 p-4 bg-white rounded-lg shadow-xs dark:bg-gray-800"
      >

        <canvas id="line"></canvas>

      </div>




    @endsection


@section('scripts')
<script>
    // Récupérer les données depuis le serveur
    fetch('/stat')
      .then(response => response.json())
      .then(data => {
        // console.log('Données récupérées:', data);  // Affiche toute la réponse du serveur pour inspecter la structure

        // Vérifier si les données sont valides
        if (data) {
          const stats = {
            New: data.New,
            completed: data.Completed,
            pending: data.Traitement,
            cancelled: data.Picked,
            paid:data.Paid
          };

          // Mettre à jour le graphique avec les données récupérées
          updateChart(stats);
        } else {
         // console.log('Aucune donnée trouvée');
        }
      })
      .catch(error => console.error('Erreur lors de la récupération des statistiques:', error));

    // Mettre à jour le graphique
    function updateChart(stats) {
      const chartData = {
        labels: ['Janv', 'Fev', 'Mars', 'Avril', 'Mai', 'Juin', 'Juillet', 'Août', 'Sept', 'Oct', 'Nov', 'Dec'],
        datasets: [
          {
            label: 'Nouvelles commandes',
            backgroundColor: '#4caf50',
            borderColor: '#4caf50',
            data: stats.New,
            fill: false,
          },
          {
            label: 'Commande terminée',
            backgroundColor: '#2196f3',
            borderColor: '#2196f3',
            data: stats.completed,
            fill: false,
          },
          {
            label: 'Commandes en traitement',
            backgroundColor: '#ffeb3b',
            borderColor: '#ffeb3b',
            data: stats.pending,
            fill: false,
          },
          {
            label: 'Commandes collectées',
            backgroundColor: '#f44336',
            borderColor: '#f44336',
            data: stats.cancelled,
            fill: false,
          },
          {
            label: 'Commandes payées',
            backgroundColor: '#4f46e5',
            borderColor: '#4f46e5',
            data: stats.paid,
            fill: false,
          },
        ],
      };

      const config = {
        type: 'line', // Graphique en lignes
        data: chartData,
        options: {
          responsive: true,
          plugins: {
            legend: {
              display: true,
            },
          },
          scales: {
            x: {
              title: {
                display: true,
                text: 'Mois',
              },
            },
            y: {
              title: {
                display: true,
                text: 'Nombre de Commandes',
              },
              beginAtZero: true,
            },
          },
        },
      };

      // Sélectionner l'élément HTML du graphique
      const ctx = document.getElementById('line').getContext('2d');
      new Chart(ctx, config);
    }
  </script>

@endsection
