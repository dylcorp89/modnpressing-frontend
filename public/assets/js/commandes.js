import { dbRef } from './firebase.js';

const commandesTable = document.getElementById('commandesTable');
const itemsPerPage = 10;
let commandesData = [];
let filteredData = [];
let currentPage = 1;

// Met à jour le tableau
export function updateTable(data) {
    commandesTable.innerHTML = data.map(commande => `
        <tr>
            <td>${commande.identifiant}</td>
            <td>${commande.nomcommande || 'Non spécifié'}</td>
            <td>${commande.statut}</td>
            <td>${commande.collecteDate ? new Date(commande.collecteDate).toLocaleDateString('fr-FR') : 'Non spécifiée'}</td>
            <td>${commande.dateajout ? new Date(commande.dateajout).toLocaleString('fr-FR') : 'Non spécifiée'}</td>
            <td>
                <a href="/commandes/details/${commande.id}" class="text-blue-500 hover:text-blue-700">
                    Voir
                </a>
            </td>
        </tr>
    `).join('');
}

// Filtre les données
export function filterData(searchValue) {
    filteredData = searchValue === ''
        ? commandesData
        : commandesData.filter(c => c.nomcommande?.toLowerCase().includes(searchValue));
    updateTable(filteredData.slice(0, itemsPerPage));
}

// Écoute les changements dans Firebase
dbRef.on('value', snapshot => {
    commandesData = Object.entries(snapshot.val() || {}).map(([key, commande]) => ({ id: key, ...commande }));
    commandesData.sort((a, b) => new Date(b.dateajout) - new Date(a.dateajout));
    updateTable(commandesData.slice(0, itemsPerPage));
});
