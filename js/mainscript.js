$(document).ready(function() {
    // Ensure userId is set correctly from the body data attribute
    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID:', userId);

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
                        alert('Rating saved successfully');
                        $('#ratingModal').modal('hide');
                    } else {
                        alert('Error saving rating: ' + res.message);
                    }
                } catch (e) {
                    console.error('Error parsing JSON:', e);
                    alert('Error parsing server response');
                }
            },
            error: function(xhr, status, error) {
                console.error('Booking error:', error);
                alert('Error booking appointment: ' + error);
            }
        });
    });

    $('.card').on('click', function() {
        const providerId = $(this).data('id'); // Get the provider ID from the clicked card
        let userId;
        $('#dataModal').data('provider-id', providerId); // Set the provider ID in the modal
        userId = $('body').data('user-id');
        console.log('Assigned userId:', userId);
      
        // Add null check and debugging
        if (!userId) {
            console.error('userId is not set properly');
            // You might want to handle this case, maybe redirect to login
        }
      
        // Show the modal
        $('#dataModal').modal('show');
    });

    (function() {
        let userId;
        let calendar;
        
        $(document).ready(function() {
            console.log('Document ready');
            
            // Check if FullCalendar is loaded
            if (typeof $.fn.fullCalendar === 'undefined') {
                console.error('FullCalendar is not loaded!');
                return;
            }
            
            // Initialize userId from PHP or data attribute
            if (typeof window.userId !== 'undefined') {
                userId = window.userId;
            } else {
                userId = $('body').data('user-id');
            }
            console.log('User ID:', userId);
      
            if (typeof window.providerId !== 'undefined') {
                provider = window.providerId;
            } else {
                providerId = $('body').data('provider-id');
            }
            console.log('Provider ID:', providerId);
        
            initializeEventHandlers();
        });

        function initializeEventHandlers() {
            console.log('Initializing event handlers');
            
            // Handle card clicks to show provider details
            $('.card').on('click', function() {
                const providerId = $(this).data('id');
                console.log('Card clicked, provider ID:', providerId);
                $('#dataModal').data('provider-id', providerId);
                
                // Fetch and display provider details
                fetchProviderDetails(providerId);
                
                // Show the modal
                $('#dataModal').modal('show');
            });
        
            // Handle book button click
            $('#book').on('click', function() {
                console.log('Book button clicked');
                
                // Clear the modal body first
                $('#modalBody').empty().append('<div id="calendar"></div>');
                
                // Hide provider details
                $('.provider-details').hide();
                
                // Initialize calendar
                initializeCalendar();
                
                // Show booking button
                $('#bookAppointment').show();
                // Hide the book button
                $(this).hide();
            });
        
            // Handle booking confirmation
            $('#bookAppointment').on('click', handleBooking);
        
            // Handle modal close - reset the view
            $('#dataModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - resetting view');
                // Reset buttons
                $('#book').show();
                $('#bookAppointment').hide();
                
                // Destroy calendar if it exists
                if (calendar) {
                    calendar.fullCalendar('destroy');
                    calendar = null;
                }
            });
        }

        function fetchProviderDetails(providerId) {
            console.log('Fetching provider details for ID:', providerId);
            $.ajax({
                url: '../controller/providerController.php?action=fetch',
                type: 'POST',
                data: { id: providerId },
                success: function(response) {
                    $('#modalBody').html(response);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching provider details:', error);
                    alert('Error loading provider details');
                }
            });
        }

        function initializeCalendar() {
            console.log('Initializing calendar');
            try {
                calendar = $('#calendar').fullCalendar({
                    header: {
                        left: 'prev,next',
                        center: 'title',
                        right: ''
                    },
                    locale: 'hu', // Set locale to Hungarian
                    buttonText: {
                        today: 'ma',
                        month: 'hónap',
                        week: 'hét',
                        day: 'nap'
                    },
                    monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
                    monthNamesShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún', 'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
                    dayNames: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
                    dayNamesShort: ['V', 'H', 'K', 'Sze', 'Cs', 'P', 'Szo'],
                    allDayText: '', // Remove "Egész nap"
                    timeFormat: 'HH:mm', // Set time format to 24-hour
                    axisFormat: 'HH:mm', // Set axis format to 24-hour
                    views: {
                        agendaDay: {
                            timeFormat: 'HH:mm', // Set time format to 24-hour for agendaDay view
                            slotLabelFormat: 'HH:mm' // Set slot label format to 24-hour for agendaDay view
                        }
                    },
                    selectable: true,
                    selectHelper: true,
                    select: handleDateSelect,
                    eventRender: function(event, element) {
                        element.find('.fc-title').append(
                            `<br/><small>${event.provider_name || ''}</small>`
                        );
                    },
                    dayClick: function(date) {
                        calendar.fullCalendar('changeView', 'agendaDay', date); // Change to day view on click
                        fetchAvailableHours(date.format('YYYY-MM-DD'));
                    }
                });
                console.log('Calendar initialized successfully');
            } catch (error) {
                console.error('Error initializing calendar:', error);
            }
        }

        function handleDateSelect(start, end) {
            const selectedDate = start.format('YYYY-MM-DD');
            const selectedTime = start.format('HH:mm');
            console.log('Date selected:', selectedDate, 'Time selected:', selectedTime);
            if (!userId) {
                alert('Please log in to book appointments');
                return;
            }
        
            const providerId = $('#dataModal').data('provider-id');
            
            if (!providerId) {
                alert('Please select a provider first');
                return;
            }

            // Store the selected date and time in a global variable or data attribute
            $('#dataModal').data('selected-date', selectedDate);
            $('#dataModal').data('selected-time', selectedTime);
        }

        function fetchAvailableHours(date) {
            console.log('Fetching available hours for date:', date);
            const providerId = $('#dataModal').data('provider-id');
            $.ajax({
                url: '../controller/providerController.php?action=fetchHours',
                type: 'POST',
                data: { date: date, provider_id: providerId },
                success: function(response) {
                    // Display available hours in the calendar
                    const events = JSON.parse(response);
                    calendar.fullCalendar('removeEvents');
                    calendar.fullCalendar('addEventSource', events);
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching available hours:', error);
                    alert('Error loading available hours');
                }
            });
        }

        function handleBooking() {
            console.log('Booking button clicked');
            const selectedDate = $('#dataModal').data('selected-date');
            const selectedTime = $('#dataModal').data('selected-time');
            const providerId = $('#dataModal').data('provider-id');
            
            if (!selectedDate || !selectedTime || !providerId) {
                alert('Please select both a date and provider');
                return;
            }
        
            if (confirm('Leszeretnéd foglalni az időpontot erre a dátumra: ' + selectedDate + ' ' + selectedTime + '?')) {
                createAppointment(selectedDate, selectedTime, providerId);
            }
        }

        function createAppointment(selectedDate, selectedTime, providerId) {
            console.log('Creating appointment for date:', selectedDate, 'time:', selectedTime, 'provider:', providerId);
            $.ajax({
                url: '../controller/providerController.php?action=book',
                type: 'POST',
                data: {
                    selectedDate: selectedDate,
                    selectedTime: selectedTime,
                    provider_id: providerId,
                    user_id: userId
                },
                success: function(response) {
                    console.log('Server response:', response);
                    alert('Sikeresen lefoglaltad az időpontot!');
                    $('#dataModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error('Booking error:', error);
                    alert('Error booking appointment: ' + error);
                }
            });
            console.log('Sending data:', {
                selectedDate: selectedDate,
                selectedTime: selectedTime,
                provider_id: providerId,
                user_id: userId
            });
        }
    })();

    /* ---------------------------------------------------------------------------account */
    function toggleEditForm() {
        var editForm = document.getElementById('edit-form');
        var accountDetails = document.getElementById('account-details');
        
        if (editForm.style.display === 'none') {
            editForm.style.display = 'block';
            accountDetails.style.display = 'none'; // Elrejti az alap adatokat
        } else {
            editForm.style.display = 'none';
            accountDetails.style.display = 'block'; // Visszaállítja az alap adatokat
        }
    }

    /* ---------------------------------------------------------------------------darkmode-lightmode */
    document.addEventListener('DOMContentLoaded', function() {
        const themeToggleBtn = document.getElementById('theme-toggle');
        const body = document.body;

        // Load the saved theme from localStorage
        const savedTheme = localStorage.getItem('theme');
        if (savedTheme) {
            body.classList.add(savedTheme);
            themeToggleBtn.textContent = savedTheme === 'dark-mode' ? 'Light Mode' : 'Dark Mode';
        } else {
            body.classList.add('light-mode');
            themeToggleBtn.textContent = 'Dark Mode';
        }

        themeToggleBtn.addEventListener('click', function() {
            if (body.classList.contains('light-mode')) {
                body.classList.remove('light-mode');
                body.classList.add('dark-mode');
                themeToggleBtn.textContent = 'Light Mode';
                localStorage.setItem('theme', 'dark-mode');
            } else {
                body.classList.remove('dark-mode');
                body.classList.add('light-mode');
                themeToggleBtn.textContent = 'Dark Mode';
                localStorage.setItem('theme', 'light-mode');
            }
        });
    });

    /* ---------------------------------------------------------------------------appointments */
    document.addEventListener('DOMContentLoaded', function() {
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

        document.querySelector('.btn-delete-confirm').addEventListener('click', function() {
            const appointmentId = this.getAttribute('data-id');
            $.ajax({
                url: '../controller/UserController.php?action=delete_appointment',
                type: 'POST',
                data: { appointment_id: appointmentId },
                success: function(response) {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        location.reload();
                    } else {
                        alert('Failed to delete appointment');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error deleting appointment');
                }
            });
        });
    });

//ratings
/* $(document).ready(function() {
    // Ensure userId is set correctly from the body data attribute
    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID:', userId);

    $('.rate-button').on('click', function() {
        var appointmentId = $(this).data('appointment-id');
        var providerId = $(this).data('provider-id');

        $('#appointment-id').val(appointmentId);
        $('#provider-id').val(providerId);

        console.log('Rate button clicked:', {
            appointmentId: appointmentId,
            providerId: providerId,
            rating: rating
        });
    });

    $('#save-rating').on('click', function() {
        var rating = $('input[name="rating"]:checked').val();
        var appointmentId = $('#appointment-id').val();
        var providerId = $('#provider-id').val();

        if (!userId || !appointmentId || !providerId || !rating) {
            console.error('Missing required fields:', {
                userId: userId,
                appointmentId: appointmentId,
                providerId: providerId,
                rating: rating
            });
            alert('Please fill in all the required fields.');
            return;
        }

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
                    //console.log('Raw response:', response);
                    const res = JSON.parse(response);
                    if (res.status === 'success') {
                        alert('Rating saved successfully');
                        $('#ratingModal').modal('hide');
                    } else {
                        alert('Error saving rating: ' + res.message);
                    }
            }
        });
    });
}); */
}); 