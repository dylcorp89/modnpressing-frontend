import { updateTable, filterData } from './commandes.js';

document.getElementById('searchInput').addEventListener('input', e => {
    filterData(e.target.value.toLowerCase());
});

document.getElementById('pagination').addEventListener('click', e => {
    if (e.target.tagName === 'BUTTON') {
        currentPage = Number(e.target.dataset.page);
        updateTable(filteredData.slice((currentPage - 1) * itemsPerPage, currentPage * itemsPerPage));
    }
});
