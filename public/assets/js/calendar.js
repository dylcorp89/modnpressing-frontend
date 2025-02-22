import { FullCalendar } from 'fullcalendar';

export function renderCalendar(events) {
    const calendarEl = document.getElementById('calendar');
    const calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'fr',
        events: events,
        eventClick: function(info) {
            const commandeId = info.event.extendedProps.commandeId;
            if (commandeId) window.location.href = `/commandes/details/${commandeId}`;
        },
    });
    calendar.render();
}
