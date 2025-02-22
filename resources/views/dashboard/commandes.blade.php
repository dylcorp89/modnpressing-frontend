@extends('app')

@section('content')
    <h2 class="flex my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200 justify-between">
        <div> Gestion des commandes</div>

        <div><button id="toggleButton" class=" text-xl px-2 py-1 bg-purple-700 text-white rounded hover:bg-purple-800">
                Afficher la liste des commandes
            </button></div>
    </h2>



    <div id="calendarCard" class="  rounded-lg max-h-screen">
        <div class="   bg-white p-4" id="calendar">

        </div>
    </div>

    <div id="listeCommande" class="mt-4 p-4 bg-gray-100 rounded shadow hidden">
        <h2 class="my-6 text-2xl font-semibold text-gray-700 dark:text-gray-200">
            Liste des commandes
        </h2>

        <!-- Recherche -->
        <div class="mb-4 bg-white p-3">
            <input type="text" id="searchInput" placeholder="Rechercher multicritères"
                class="w-full px-4 py-2 border rounded-lg dark:bg-gray-800 dark:border-gray-700" />
        </div>

        <!-- Table -->
        <div class="w-full overflow-hidden rounded-lg shadow-xs">
            <div class="w-full overflow-x-auto">
                <table class="w-full whitespace-no-wrap">
                    <thead>
                        <tr
                            class="text-xs font-semibold tracking-wide text-left text-gray-500 uppercase border-b dark:border-gray-700 bg-gray-50 dark:text-gray-400 dark:bg-gray-800">
                            <th class="px-4 py-3">Identifiant</th>
                            <th class="px-4 py-3">Nom service</th>
                            <th class="px-4 py-3">Statut</th>
                            <th class="px-4 py-3">Date rendez-vous</th>
                            <th class="px-4 py-3">Créneaux</th>
                            <th class="px-4 py-3">Ajouté le</th>
                            <th class="px-4 py-3 text-center">Action</th>
                        </tr>
                    </thead>
                    <tbody id="commandesTable" class="bg-white divide-y dark:divide-gray-700 dark:bg-gray-800">
                        <!-- Les lignes seront insérées ici -->
                    </tbody>
                </table>
            </div>
            <div id="pagination" class="flex justify-center mt-4 space-x-2">
                <!-- Les boutons de pagination seront insérés ici -->
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-app.js"></script>
    <script src="https://www.gstatic.com/firebasejs/8.10.1/firebase-database.js"></script>
    <!-- Toastr CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <!-- Toastr JS -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        // Récupérer les éléments du DOM
        const toggleButton = document.getElementById('toggleButton');
        const listeCommande = document.getElementById('listeCommande');
        const calendarCard = document.getElementById('calendarCard');


        // Ajouter un événement de clic au bouton
        toggleButton.addEventListener('click', () => {
            // Basculer la classe 'hidden' sur la div listeCommande
            listeCommande.classList.toggle('hidden');
            calendarCard.classList.toggle('hidden');
            // Mettre à jour le texte du bouton en fonction de l'état de la liste
            toggleButton.textContent = listeCommande.classList.contains('hidden') ?
                ' Afficher la liste des commandes' : 'Cacher la liste des commandes';
        });
        const firebaseConfig = {
            apiKey: "AIzaSyAuqy...",
            authDomain: "modn-pressing-d48f9.firebaseapp.com",
            databaseURL: "https://modn-pressing-d48f9-default-rtdb.firebaseio.com",
            projectId: "modn-pressing-d48f9",
            storageBucket: "modn-pressing-d48f9.appspot.com",
            messagingSenderId: "645101488086",
            appId: "1:645101488086:web:336a734151659f195f4677",
            measurementId: "G-E4CGQLNBQB"
        };

        firebase.initializeApp(firebaseConfig);
        const dbRef = firebase.database().ref('commandes');
        const dbRefUser = firebase.database().ref('users');

        const itemsPerPage = 10;
        let currentPage = 1;
        let commandesData = [];
        let filteredData = [];
        let previousCommandesData = [];
        const calendarEl = document.getElementById('calendar');
        const commandesTable = document.getElementById('commandesTable');

        const pagination = document.getElementById('pagination');
        const totalCommandesContainer = document.getElementById('total-commandes-container');
        const now = new Date();

        function convertirEnDateISO(date) {
            return new Date(date).toISOString().split('T')[0];
        }

        function renderCalendar(events) {
            const calendarEl = document.getElementById('calendar');

            // Effacer le contenu précédent du calendrier (si nécessaire)
            if (calendarEl.innerHTML) {
                calendarEl.innerHTML = '';
            }

            const calendar = new FullCalendar.Calendar(calendarEl, {

                initialView: 'dayGridMonth',
                editable: true,

                themeSystem: 'standard',
                locale: 'fr', // Définir la langue en français
                droppable: true,
                headerToolbar: {
                    left: 'prev,next today',
                    center: 'title',
                    right: 'dayGridMonth,timeGridWeek,timeGridDay,listWeek',

                },
                buttonText: {
                    today: "Aujourd'hui",
                    month: 'Mois',
                    week: 'Semaine',
                    day: 'Jour',
                    list: 'Liste',
                },

                eventMouseLeave: function() {
                    const tooltip = document.getElementById('tooltip');
                    if (tooltip) {
                        tooltip.remove();
                    }
                },

                eventClick: function(info) {
                    // Récupérer l'ID de la commande depuis extendedProps
                    const commandeId = info.event.extendedProps.commandeId;

                    //console.log(commandeId);

                    // Vérifier si l'ID de la commande est disponible
                    if (commandeId) {
                        // Rediriger vers la page des détails
                        window.location.href = `/app/public/commandes/details/${commandeId}`;

                    } else {
                        console.error('ID de commande non trouvé pour cet événement.');
                    }
                },

                events: events,

                eventContent: function(args) {
                    const {
                        backgroundColor,
                        textColor,
                        circleColor
                    } = getEventColor(args.event.extendedProps.status);

                    return {
                        html: `
            <div style="background: ${backgroundColor}; color: ${textColor};  width: 100%; padding: 10px; border-radius: 4px;">
                <div style="display: flex; align-items: center;">
                    <div style="width: 12px; height: 12px; background-color: ${circleColor};box-shadow: 0 2px 5px rgba(225,225,225,0.95); border-radius: 50%; border:1px solid white; margin-right: 10px;
                        box-shadow: 0 0 8px rgba(0, 0, 0, 0.2);"></div>
                    <div style="font-weight: bold; text-transform: uppercase;">${args.event.title || ''}</div>
                </div>
                <div>${args.event.extendedProps.commandeIdentifiant || ''}</div>
                <div>${args.event.extendedProps.phone || ''}</div>
                <div>${args.event.extendedProps.description || ''}</div>
                <div>${args.event.extendedProps.commandeRegistre || ''}</div>
            </div>
        `
                    };
                },
                eventDrop: function(info) {
                    const eventId = info.event.id; // ID unique de l'événement
                    const newStart = info.event.start; // Nouvelle date de début
                    const newEnd = info.event.end; // Nouvelle date de fin (si applicable)
                    const oldEvent = info.oldEvent; // Informations avant la mise à jour (si disponible)

                    if (!eventId || !newStart) {
                        console.error("Impossible de récupérer l'ID de l'événement ou la nouvelle date.");
                        return;
                    }

                    // Convertir la nouvelle date en ISO
                    const newDateISO = newStart.toISOString().split('T')[0];
                    let newCreneau = '';

                    // Si une plage horaire est spécifiée, la convertir en créneau
                    if (newStart && newEnd) {
                        const startHour = newStart.getHours();
                        const endHour = newEnd.getHours();
                        newCreneau = `${startHour}h - ${endHour}h`;
                    }

                    // Vérifier si c'est une nouvelle commande (absence d'ancien événement)
                    const isNewCommand = !oldEvent;

                    // Mise à jour des données dans Firebase (ou une autre base de données)
                    firebase.database().ref(`commandes/${eventId}`).update({
                        collecteDate: newDateISO,
                        collecteCreneau: newCreneau,
                    }).then(() => {
                        // Notification de succès
                        toastr.success("Événement mis à jour avec succès !");

                        // Afficher une alerte uniquement pour une nouvelle commande
                        if (isNewCommand) {
                            Swal.fire({
                                icon: "info",
                                title: `Nouvelle commande`,
                                html: `
                    <strong>Type de collecte :</strong> ${info.event.extendedProps.nomcommande || 'Sans titre'}<br>
                    <strong>Date de collecte :</strong>${newStart.toLocaleDateString('fr-FR')}<br>
                    <strong>Créneau :</strong> ${newCreneau || 'Non défini'}
                `,
                                showConfirmButton: false,
                                timer: 5000
                            });
                        }
                    }).catch((error) => {
                        console.error("Erreur lors de la mise à jour de l'événement :", error);
                        toastr.error("Erreur lors de la mise à jour de l'événement.");

                        // Annuler le déplacement en cas d'erreur
                        info.revert();
                    });
                },
                // Ajout des événements dynamiques
            });

            calendar.render();
        }

        function updateTable(data) {
            commandesTable.innerHTML = data.map(commande => `
            <tr>
                <td class="px-4 py-3 text-sm">${commande.identifiant}</td>
                <td class="px-4 py-3 text-sm">${commande.nomcommande || 'Non spécifié'}</td>
                <td class="px-4 py-3 text-sm">
                    <span class="px-2 py-1 font-semibold leading-tight rounded-full text-sm ${getStatutClass(commande.statut)}">
                        ${commande.statut}
                    </span>
                </td>
                <td class="px-4 py-3 text-sm">  ${commande.collecteDate ? new Date(commande.collecteDate).toLocaleDateString('fr-FR') : 'Non spécifiée'}</td>
                <td class="px-4 py-3 text-sm">${commande.collecteCreneau || ''}</td>
                <td class="px-4 py-3 text-sm">  ${commande.dateajout ? new Date(commande.dateajout).toLocaleString('fr-FR') : 'Non spécifiée'}</td>
                <td class="px-4 py-3 text-center">

                   <a href="/app/public/commandes/details/${commande.id}" class="text-blue-500 hover:text-blue-700">
            <i class="fa-solid fa-eye fa-2x text-orange-500"></i>
        </a>
                </td>
            </tr>
        `).join('');
        }

        function setupPagination(data) {
            const totalPages = Math.ceil(data.length / itemsPerPage);

            // Clear and dynamically create pagination buttons
            pagination.innerHTML = Array.from({
                length: totalPages
            }, (_, i) => `
        <button
            class="px-3 py-1 rounded-md ${i + 1 === currentPage ? 'bg-blue-500 text-white' : 'bg-gray-200 text-gray-700 hover:bg-gray-300'}"
            data-page="${i + 1}">
            ${i + 1}
        </button>
    `).join('');

            // Add event listeners to the buttons
            const buttons = pagination.querySelectorAll('button');
            buttons.forEach(button => {
                button.addEventListener('click', (event) => {
                    currentPage = parseInt(event.target.dataset.page, 10);
                    const startIndex = (currentPage - 1) * itemsPerPage;
                    const endIndex = startIndex + itemsPerPage;
                    const paginatedData = data.slice(startIndex, endIndex);

                    updateTable(paginatedData);
                    setupPagination(data); // Refresh pagination to update the active state
                });
            });
        }








        dbRef.on('value', async (snapshot) => {
            try {
                const rawData = snapshot.val() || {};
                //console.log("Données brutes Firebase :", rawData);
                // Construction de commandesData en incluant l'ID Firebase
                commandesData = Object.entries(snapshot.val() || {}).map(([key, commande]) => ({
                    id: key, // Conserver l'ID Firebase
                    ...commande,
                    dateAjoutLisible: formatDateAjout(commande.dateajout),
                }));
                //console.log("Commandes transformées :", commandesData);


                // Trier les commandes par ordre décroissant (basé sur le champ `dateajout`)
                commandesData.sort((a, b) => {
                    const dateA = new Date(a.dateajout || 0);
                    const dateB = new Date(b.dateajout || 0);
                    return dateB - dateA; // Tri décroissant
                });

                // Filtrer les données pour exclure celles avec le statut "Paid"

                filteredData = commandesData;
    updateTable(filteredData.slice(0, itemsPerPage));


                // Mettre à jour la pagination
                setupPagination(filteredData);
                // Afficher la dernière commande

                const commandesDataForCalendar = commandesData.filter(commande => commande.statut !== 'Paid');
                const lastCommande = commandesData[0];
                // console.log("Dernière commande :", lastCommande);
                //toastr.success(`Nouvelle commande : ${lastCommande.nomcommande || 'Sans titre'}`);
                Swal.fire({
                    icon: "info",
                    title: `Nouvelle commande`,
                    html: `
        <strong>Type de collecte :</strong> ${lastCommande.nomcommande || 'Sans titre'}<br>
        <strong>Date de collecte :</strong>${lastCommande.collecteDate ? new Date(lastCommande.collecteDate).toLocaleDateString('fr-FR') : 'Non spécifiée'}<br>

        <strong>Créneau :</strong> ${lastCommande.collecteCreneau || 'Non défini'}
    `,
                    showConfirmButton: false,
                    timer: 5000
                });

                // Génération des événements pour le calendrier
                const events = await Promise.all(
                    commandesDataForCalendar.map(async (commande) => {


                        const startDate = convertirEnDateISO(commande.collecteDate);
                        const creneau = commande.collecteCreneau || ''; // Exemple : "10h - 12h"

                        let startTime = null;
                        let endTime = null;

                        // Gestion des créneaux horaires
                        try {
                            const regex = /(\d{1,2})h\s*-\s*(\d{1,2})h/;
                            const match = creneau.match(regex);

                            if (match) {
                                const [_, startHour, endHour] = match
                                ; // Groupes capturés par le regex
                                const startHourInt = parseInt(startHour, 10);
                                const endHourInt = parseInt(endHour, 10);

                                if (!isNaN(startHourInt) && !isNaN(endHourInt)) {
                                    // Formater les heures au format HH:mm:ss
                                    startTime = `${startHourInt.toString().padStart(2, '0')}:00:00`;
                                    endTime = `${endHourInt.toString().padStart(2, '0')}:00:00`;
                                } else {
                                    // console.error('Les heures extraites ne sont pas valides.');
                                }
                            } else {
                                //console.warn('Le créneau horaire n’a pas été trouvé dans le format attendu.');
                            }
                        } catch (error) {
                            //console.error('Erreur lors de la récupération des créneaux horaires :', error);
                        }

                        // Construction des dates complètes si startTime et endTime sont valides
                        if (startTime && endTime) {
                            const startDateTime = `${startDate}T${startTime}`;
                            const endDateTime = `${startDate}T${endTime}`;

                            // console.log('Start:', startDateTime, 'End:', endDateTime);
                        } else {
                            // console.warn('Les dates/heures complètes n’ont pas pu être construites.');
                        }

                        // Récupérer le nom de l'utilisateur (fonction asynchrone)
                        const userInfo = await getUserInfo(commande.iduser).catch(err => {
                            //  console.error(`Erreur pour l'utilisateur ${commande.iduser}:`, err);
                            return {
                                name: 'Utilisateur inconnu',
                                phone: 'Inconnu'
                            };
                        });;


                        const event = {
                            id: commande.id,
                            title: `${userInfo.name || ''} `,
                            phone: `${userInfo.phone || ''} `,
                            commandeRegistre: formatDateAjout(commande.dateajout) || "Inconnu",
                            commandeId: commande.id || 'Inconnu',
                            commandeIdentifiant: commande.nomcommande || 'Inconnu',
                            start: startTime ? `${startDate}T${startTime}` :
                                `${startDate}T00:00`,
                            end: endTime ? `${startDate}T${endTime}` : `${startDate}T23:59`,
                            description: commande.anotherInfo || 'Créneau non spécifié',
                            status: commande.statut || 'Statut inconnu',
                            extendedProps: {
                                userId: commande.iduser,
                            },
                            backgroundColor: getEventColor(commande.statut).backgroundColor,
                            textColor: getEventColor(commande.statut).textColor,
                            eventColor: getEventColor(commande.statut).backgroundColor,
                            borderColor: getEventColor(commande.statut).backgroundColor,
                        }
                        return event;
                    })
                );
                //console.log("Événements pour le calendrier :", events);
                // saveToFile(events, "events.json");
                // Filtrer les événements qui sont null (statut "Paid")
                const filteredEvents = events.filter(event => event !== null);

                // Passer les événements filtrés à la fonction pour afficher dans le calendrier
                renderCalendar(filteredEvents);

            } catch (error) {
                console.error("Erreur lors de la récupération des données Firebase :", error);
            }
        });


 // Filtrage des données
 function filterData(searchValue) {
    console.log('Valeur de recherche :', searchValue);
    console.log('Données initiales :', commandesData);

    // Réinitialiser si searchValue est vide
    if (!searchValue) {
        console.log('Valeur de recherche vide, réinitialisation des données.');
        filteredData = commandesData;
    } else {
        // Appliquer le filtre
        filteredData = commandesData.filter(c => {
            const identifiant = c.identifiant ? c.identifiant.toLowerCase() : '';
            const nomCommande = c.nomcommande ? c.nomcommande.toLowerCase() : '';
            const statut = c.statut ? c.statut.toLowerCase() : '';

            const identifiantMatch = identifiant.includes(searchValue);
            const nomCommandeMatch = nomCommande.includes(searchValue);
            const statutMatch = statut.includes(searchValue);

            console.log({
                identifiant,
                nomCommande,
                statut,
                searchValue,
                identifiantMatch,
                nomCommandeMatch,
                statutMatch,
            });

            return identifiantMatch || nomCommandeMatch || statutMatch;
        });
    }

    // Gérer les cas sans résultats
    if (filteredData.length === 0) {
        commandesTable.innerHTML = '<tr><td colspan="7" class="text-center py-4">Aucun résultat trouvé.</td></tr>';
        pagination.innerHTML = '';
        console.log('Aucun résultat trouvé.');
        return;
    }

    // Mettre à jour la table et la pagination
    console.log('Données filtrées :', filteredData);
    updateTable(filteredData.slice(0, itemsPerPage));
    setupPagination(filteredData);
}




        pagination.addEventListener('click', e => {
            if (e.target.tagName === 'BUTTON') {
                currentPage = Number(e.target.dataset.page);
                updateTable(filteredData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage));
            }
        });

        //fonction pour la recherhce
        document.addEventListener('DOMContentLoaded', () => {
            const searchInput = document.getElementById('searchInput');

            if (!searchInput) {
                console.error("L'élément #searchInput n'existe pas.");

            }

            // Ajouter un événement sur le champ de recherche
            searchInput.addEventListener('input', e => {
                const searchValue = e.target.value.trim().toLowerCase(); // Nettoyer la valeur entrée
                filterData(searchValue); // Appeler la fonction de filtrage
            });
        });








        // Cache en mémoire pour les utilisateurs
        const userCache = {};

        // Fonction pour récupérer les informations utilisateur
        async function getUserInfo(userId) {
            try {
                // Vérifier si les données de l'utilisateur sont déjà en cache
                if (userCache[userId]) {
                    //  console.log(`Données en cache trouvées pour l'utilisateur : ${userId}`);
                    return userCache[userId];
                }

                // console.log(`Récupération des informations pour l'utilisateur : ${userId}`);
                const refPath = `users/${userId}/info`;

                const snapshot = await firebase.database().ref(refPath).once('value');

                if (!snapshot.exists()) {
                    // console.warn(`Aucune donnée trouvée pour l'utilisateur avec l'ID : ${userId}`);
                    return {
                        name: 'Inconnu',
                        phone: 'Non spécifié'
                    };
                }

                const userInfo = snapshot.val();
                const name = userInfo?.name || 'Inconnu';
                const phone = userInfo?.telephone || 'Non spécifié';

                // Ajouter les informations au cache
                userCache[userId] = {
                    name,
                    phone
                };

                //  console.log(`Utilisateur ajouté au cache : ${userId}`);
                return {
                    name,
                    phone
                };
            } catch (error) {
                // console.error(`Erreur lors de la récupération des informations pour l'utilisateur ${userId} :`, error);
                return {
                    name: 'Inconnu',
                    phone: 'Non spécifié'
                };
            }
        }




        function getStatutClass(statut) {
            switch (statut) {
                case 'New':
                    return 'text-gray-700 bg-blue-100';
                case 'Paid':
                    return 'text-green-700 bg-green-100';
                case 'Completed':
                    return 'text-orange-800 bg-orange-100';
                case 'Traitement':
                    return 'text-red-700 bg-yellow-100';
                case 'Picked':
                    return 'text-red-700 bg-red-100';
                default:
                    return 'text-white-700 bg-purple-100';
            }
        }

        // Fonction pour convertir une date en ISO
        function convertirEnDateISO(date) {
            if (!date) return null;
            const parsedDate = new Date(date);
            return !isNaN(parsedDate) ? parsedDate.toISOString().split('T')[0] : null;
        }

        // Fonction pour formater une date à partir d'un timestamp
        function formatDateAjout(timestamp) {
            if (!timestamp) return "Date inconnue"; // Si le champ est vide ou invalide

            const date = new Date(timestamp);
            if (isNaN(date.getTime())) return "Date invalide"; // Vérifie si la date est valide

            // Formater la date en JJ/MM/AAAA HH:mm
            const options = {
                day: "2-digit",
                month: "2-digit",
                year: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            };
            return date.toLocaleString("fr-FR", options);
        }



        // Fonction pour déterminer la classe Tailwind CSS selon le statut
        function getEventColor(status) {
            switch (status) {
                case 'New':
                    return {
                        backgroundColor: 'linear-gradient(135deg, #6C91BF 0%, #A3B9D5 100%)',
                            textColor: '#ffffff',
                            circleColor: '#6C91BF' // Bleu doux
                    };
                case 'Paid':
                    return {
                        backgroundColor: 'linear-gradient(135deg, #6ECF68 0%, #A0E47F 100%)',
                            textColor: '#ffffff',
                            circleColor: '#6ECF68' // Vert frais
                    };
                case 'Completed':
                    return {
                        backgroundColor: 'linear-gradient(135deg, #A3D4B5 0%, #D8EACF 100%)',
                            textColor: '#ffffff',
                            circleColor: '#A3D4B5' // Orange vif
                    };
                case 'Traitement':
                    return {
                        backgroundColor: 'linear-gradient(135deg, #00C4CC 0%, #00E1D9 100%)',
                            textColor: '#ffffff',
                            circleColor: '#00C4CC' // Bleu turquoise
                    };
                case 'Picked':
                    return {
                        backgroundColor: 'linear-gradient(135deg, #FFA500 0%, #FFB84D 100%)',
                            textColor: '#ffffff',
                            circleColor: '#FFA500' // Rouge éclatant
                    };
                default:
                    return {
                        backgroundColor: 'linear-gradient(135deg, #B3B3B3 0%, #D1D1D1 100%)',
                            textColor: '#ffffff',
                            circleColor: '#B3B3B3' // Gris clair
                    };
            }
        }



        toastr.options = {
            "positionClass": "toast-top-right",
            "timeOut": "5000",

            // Autres options de configuration
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        function saveToFile(data, filename = "events.json") {
            const blob = new Blob([JSON.stringify(data, null, 2)], {
                type: "application/json"
            });
            const link = document.createElement("a");
            link.href = URL.createObjectURL(blob);
            link.download = filename;
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
        }
    </script>
@endsection
