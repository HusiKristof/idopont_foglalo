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
        console.log('Rate button clicked:', { appointmentId, providerId });

        $('#ratingModal #appointment-id').val(appointmentId);
        $('#ratingModal #provider-id').val(providerId);
    });

    $('#save-rating').on('click', function() {
        var appointmentId = $('#ratingModal #appointment-id').val();
        var providerId = $('#ratingModal #provider-id').val();
        var rating = $('input[name="rating"]:checked').val();

        if (!rating) {
            alert('Please select a rating');
            return;
        }

        console.log('Saving rating:', { appointmentId, providerId, rating });

        $.ajax({
            url: '../controller/appointmentController.php?action=save_rating',
            type: 'POST',
            dataType: 'json',
            data: {
                appointment_id: appointmentId,
                provider_id: providerId,
                rating: rating
            },
            success: function(response) {
                console.log('Rating save response:', response);
                if (response.status === 'success') {
                    alert('Értékelés sikeresen mentve!');
                    $('#ratingModal').modal('hide');
                    // Optionally reload the page to show updated ratings
                    location.reload();
                } else {
                    alert('Hiba történt az értékelés mentésekor: ' + response.message);
                }
            },
            error: function(xhr, status, error) {
                console.error('Error details:', {
                    xhr: xhr.responseText,
                    status: status,
                    error: error
                });
                alert('Hiba történt az értékelés mentésekor!');
            }
        });
    });
});