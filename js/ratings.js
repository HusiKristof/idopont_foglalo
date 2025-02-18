$(document).ready(function() {
    // Ensure userId is set correctly from the body data attribute
    let userId;
    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID (ratings.js):', userId);

    // Initialize appointment cards
    const appointmentCards = document.querySelectorAll('.appointment-card');

    appointmentCards.forEach(card => {
        const dateElement = card.querySelector('.appointment-date');
        const timeElement = card.querySelector('.appointment-time');
        const rateButton = card.querySelector('.rate-button');
        const deleteButton = card.querySelector('.delete-button');

        if (dateElement && timeElement && rateButton) {
            // Combine date and time to create a full appointment date
            const appointmentDate = new Date(`${dateElement.textContent} ${timeElement.textContent}`);
            const currentDate = new Date();

            // Check if the appointment is in the future
            if (appointmentDate > currentDate) {
                // Hide the rate button if the appointment is in the future
                rateButton.style.display = 'none';
            }
        }
    });

    $('.rate-button').on('click', function() {
        var appointmentId = $(this).data('appointment-id');
        var providerId = $(this).data('provider-id');

        $('#appointment-id').val(appointmentId);
        $('#provider-id').val(providerId);

        console.log('Rate button clicked:', {
            appointmentId: appointmentId,
            providerId: providerId
        });
    });

    $('#save-rating').on('click', function() {
        var rating = $('input[name="rating"]:checked').val();
        var appointmentId = $('#appointment-id').val();
        var providerId = $('#provider-id').val();
    
        console.log('Sending data:', {
            user_id: userId,
            appointment_id: appointmentId,
            provider_id: providerId,
            rating: rating
        });
    
        $.ajax({
            url: '../controller/AppointmentController.php?action=save_rating',
            type: 'POST',
            data: {
                user_id: userId,
                appointment_id: appointmentId,
                provider_id: providerId,
                rating: rating
            },
            success: function(response) {
                console.log('Raw response:', response); // Log the raw response
                try {
                    // Ensure response is a string before trimming
                    const responseText = typeof response === 'string' ? response : JSON.stringify(response);
                    const trimmedResponse = responseText.trim(); // Trim the response
                    console.log('Trimmed response:', trimmedResponse); // Log the trimmed response
                    const res = JSON.parse(trimmedResponse); // Parse the trimmed response
                    if (res.status === 'success') {
                        showAlert('Sikeresen elküldted az értékelést!', 'success');
                        $('#ratingModal').modal('hide');
                        location.reload();
                    } else {
                        showAlert('Error saving rating: ' + res.message, 'error');
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    showAlert('Error parsing server response', 'error');
                }
            },
            error: function(xhr, status, error) {
                console.error('Booking error:', error);
                showAlert('Error booking appointment: ' + error, 'error');
            }
        });
    });
});