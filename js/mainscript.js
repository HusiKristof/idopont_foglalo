$('.card').on('click', function() {
    const providerId = $(this).data('id'); // Get the provider ID from the clicked card

    $.ajax({
        url: '../controller/ProviderController.php?action=fetch', // Ensure this path is correct
        type: 'POST',
        data: { id: providerId },
        success: function(response) {
            $('#modalBody').html(response);
            $('#dataModal').modal('show');
        },
        error: function() {
            $('#modalBody').html('<p>Hiba történt az adatok betöltésekor.</p>');
            $('#dataModal').modal('show');
        }
    });
});

let currentMonth = 0; // Kezdő hónap (0 = Január)
const year = 2025; // Az év, amit meg akarunk jeleníteni

function generateCalendar(month) {
    const calendarEl = document.getElementById('calendar');
    const monthNames = ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'];

    // Naptár generálása
    let calendarHTML = `<h3>${monthNames[month]} ${year}</h3>`;
    calendarHTML += '<table class="table table-bordered">';
    calendarHTML += '<thead><tr>';
    ['H', 'K', 'Sze', 'Cs', 'P', 'Szo', 'V'].forEach(day => {
        calendarHTML += `<th>${day}</th>`;
    });
    calendarHTML += '</tr></thead><tbody>';

    const firstDay = new Date(year, month, 1).getDay();
    const daysInMonth = new Date(year, month + 1, 0).getDate();

    let day = 1;
    for (let i = 0; i < 6; i++) {
        calendarHTML += '<tr>';
        for (let j = 0; j < 7; j++) {
            if (i === 0 && j < firstDay) {
                calendarHTML += '<td></td>';
            } else if (day > daysInMonth) {
                calendarHTML += '<td></td>';
            } else {
                calendarHTML += `<td class="calendar-day" data-day="${day}" data-month="${month}" data-year="${year}">${day}</td>`;
                day++;
            }
        }
        calendarHTML += '</tr>';
    }
    calendarHTML += '</tbody></table>';

    calendarEl.innerHTML = calendarHTML;

    function generateCalendar(month) {
        // ... existing code ...
    
        // Kattintás esemény a napokra
        $('.calendar-day').on('click', function() {
            const day = $(this).data('day');
            const month = $(this).data('month');
            const year = $(this).data('year');
            const selectedDate = `${year}-${month + 1}-${day}`; // Format the date as YYYY-MM-DD
    
            const providerId = $('#dataModal').data('provider-id'); // Get the provider ID from the modal
    
            // AJAX request to save the appointment
            $.ajax({
                url: '../controller/AppointmentController.php', // Ensure this path is correct
                type: 'POST',
                data: {
                    date: selectedDate,
                    user_id: userId,
                    provider_id: providerId
                },
                success: function(response) {
                    alert('Időpont sikeresen lefoglalva: ' + selectedDate);
                    $('#dataModal').modal('hide'); // Close the modal after successful booking
                },
                error: function() {
                    alert('Hiba történt az időpont foglalásakor.');
                }
            });
        });
    }
}

// Hónap váltás
function changeMonth(direction) {
    currentMonth += direction;
    if (currentMonth < 0) {
        currentMonth = 11; // Vissza a decemberhez
    } else if (currentMonth > 11) {
        currentMonth = 0; // Vissza a januárhoz
    }
    generateCalendar(currentMonth);
}

// Események a modal megnyitásakor
$('#dataModal').on('shown.bs.modal', function () {
    generateCalendar(currentMonth); // Generálja a naptárat a megnyitáskor

    // Hónap váltó gombok
    const monthControls = `
        <button id="prevMonth" class="btn btn-primary">Előző hónap</button>
        <button id="nextMonth" class="btn btn-primary">Következő hónap</button>
    `;
    $('#calendar').before(monthControls);

    // Események a hónap váltó gombokhoz
    $('#prevMonth').on('click', function() {
        changeMonth(-1);
    });

    $('#nextMonth').on('click', function() {
        changeMonth(1);
    });
});