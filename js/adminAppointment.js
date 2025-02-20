$(document).ready(function() {

    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID:', userId);

    $('.confirm-button').on('click', function() {
        var appointmentId = $(this).data('id');
        console.log('Confirm appointment:', appointmentId);
        updateAppointmentStatus(appointmentId, 'confirmed');
    });

    // Elutasítás gomb kezelése
    $('.reject-button').on('click', function() {
        var appointmentId = $(this).data('id');
        updateAppointmentStatus(appointmentId, 'cancelled');
    });

    // Törlés gomb kezelése
    $('.delete-button').on('click', function() {
        var appointmentId = $(this).data('id');
        deleteAppointment(appointmentId);
    });

    // Státusz frissítése AJAX kéréssel
    function updateAppointmentStatus(appointmentId, status) {
        $.ajax({
            url: '../controller/AppointmentController.php?action=update_status',
            type: 'POST',
            data: {
                appointment_id: appointmentId,
                status: status
            },
            success: function(response) {
                if (response.status === 'success') {
                    showAlert("Sikeresen frissítetted a foglalás állapotát!", 'success');
                    location.reload(); // Oldal frissítése
                } else {
                    showAlert("Hiba történt a státusz frissítése közben.", 'error');
                    console.log(response);
                }
            },
            error: function(xhr, status, error) {
                showAlert("Hiba történt a státusz frissítése közben.", 'error');
                console.log(error);
            }
        });
    }

    // Törlés kezelése AJAX kéréssel
    function deleteAppointment(appointmentId) {
        $.ajax({
            url: '../controller/AppointmentController.php?action=delete_appointment',
            type: 'POST',
            data: {
                appointment_id: appointmentId
            },
            success: function(response) {
                if (response.status === 'success') {
                    showAlert("Sikeresen törölted a foglalást!", 'success');
                    location.reload(); // Oldal frissítése
                } else {
                    showAlert("Hiba történt a törlés közben.", 'error');
                }
            },
            error: function(xhr, status, error) {
                showAlert("Hiba történt a törlés közben.", 'error');
            }
        });
    }
});