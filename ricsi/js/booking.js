$(document).ready(function() {
    let userId;
    let calendar;

    // Initialize userId
    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID (booking.js):', userId);

    // Card click handling
    $('.card').on('click', function() {
        const providerId = $(this).data('id'); // Get the provider ID from the clicked card
        $('#dataModal').data('provider-id', providerId); // Set the provider ID in the modal
        
        if (!userId) {
            console.error('userId is not set properly');
        }
        
        $('#dataModal').modal('show');
    });

    (function() {
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
                    showAlert('Error loading provider details', 'error');
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
                    showAlert('Error loading available hours', 'error');
                }
            });
        }

        function handleBooking() {
            console.log('Booking button clicked');
            const selectedDate = $('#dataModal').data('selected-date');
            const selectedTime = $('#dataModal').data('selected-time');
            const providerId = $('#dataModal').data('provider-id');
            
            if (!selectedDate || !selectedTime || !providerId) {
                showAlert('Please select both a date and provider', 'error');
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
                    showAlert('Sikeresen lefoglaltad az időpontot!', 'success');
                    $('#dataModal').modal('hide');
                },
                error: function(xhr, status, error) {
                    console.error('Booking error:', error);
                    showAlert('Error booking appointment: ' + error, 'error');
                }
            });
            console.log('Sending data:', {
                selectedDate: selectedDate,
                selectedTime: selectedTime,
                provider_id: providerId,
                user_id: userId
            });
        }
        $(document).ready(function() {
            initializeEventHandlers();
        });
    })();
});