document.addEventListener('DOMContentLoaded', function() {
    const appointmentCards = document.querySelectorAll('.appointment-card');

    appointmentCards.forEach(card => {
        const dateElement = card.querySelector('.appointment-date');
        const timeElement = card.querySelector('.appointment-time');
        const rateButton = card.querySelector('.rate-button');

        if (dateElement && timeElement && rateButton) {
            const appointmentDate = new Date(dateElement.textContent + ' ' + timeElement.textContent);
            const currentDate = new Date();

            if (appointmentDate > currentDate) {
                rateButton.style.display = 'none';
            }
        }
    });
});