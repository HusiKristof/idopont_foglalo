$(document).ready(function() {
    /* ---------------------------------------------------------------------------appointments */
    const appointmentCards = document.querySelectorAll('.appointment-card');

    appointmentCards.forEach(card => {
        const dateElement = card.querySelector('.appointment-date');
        const timeElement = card.querySelector('.appointment-time');
        const rateButton = card.querySelector('.rate-button');
        const deleteButton = card.querySelector('.delete-button');

        if (dateElement && timeElement && rateButton && deleteButton) {
            const appointmentDate = new Date(dateElement.textContent + ' ' + timeElement.textContent);
            const currentDate = new Date();

            if (appointmentDate > currentDate) {
                rateButton.style.display = 'none';
            }

            deleteButton.addEventListener('click', function() {
                const appointmentId = deleteButton.getAttribute('data-id');
                document.querySelector('.btn-delete-confirm').setAttribute('data-id', appointmentId);
            });
        }
    });

    $('.btn-delete-confirm').on('click', function() {
        const appointmentId = $(this).data('id');
        $.ajax({
            url: '../controller/UserController.php?action=delete_appointment',
            type: 'POST',
            data: { appointment_id: appointmentId },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        showAlert('Sikeresen törölted az időpontot!', 'success');
                        location.reload();
                    } else {
                        showAlert('Failed to delete appointment', 'error');
                    }
                } catch (e) {
                    showAlert('Error deleting appointment', 'error');
                }
            },
            error: function() {
                showAlert('Error deleting appointment', 'error');
            }
        });
    });
});