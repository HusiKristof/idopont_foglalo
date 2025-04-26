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

            // Handle edit service button click
            $('#editServiceBtn').on('click', function() {
                const providerId = $('#dataModal').data('provider-id');
                // Fetch provider details again to prefill
                $.ajax({
                    url: '../controller/providerController.php?action=fetch',
                    type: 'POST',
                    data: { id: providerId },
                    success: function(providerResponse) {
                        const provider = typeof providerResponse === 'string'
                            ? JSON.parse(providerResponse)
                            : providerResponse;

                        // Prefill the addServiceForm fields
                        $('#serviceType').val(provider.type);
                        $('#serviceName').val(provider.name);
                        $('#serviceDescription').val(provider.description);
                        $('#serviceWorkingHours').val(provider.working_hours);
                        $('#serviceAddress').val(provider.address);
                        $('#servicePrice').val(provider.price);
                        $('#serviceDuration').val(provider.duration);
                        $('#servicePhone').val(provider.phone_number || '');

                        // Disable image upload
                        $('#serviceImage').prop('disabled', true).closest('.mb-3').hide();

                        // Set edit mode
                        $('#addServiceForm').data('edit-id', providerId);

                        // Change save button text
                        $('#saveService').text('Mentés (Szerkesztés)');

                        // Show modal
                        $('#addServiceModal').modal('show');
                    }
                });
            });

            $('#deleteServiceBtn').on('click', function() {
                $('#dataModal').modal('hide');
                setTimeout(function() {
                    $('#deleteModal').modal('show');
                }, 400); // Wait for hide animation
            });

            // If canceled, restore the service modal
            $('#deleteModal').on('hidden.bs.modal', function() {
                if (!$('#dataModal').hasClass('show')) {
                    setTimeout(function() {
                        $('#dataModal').modal('show');
                    }, 200);
                }
            });

            // On confirm, delete and close both modals
            $('.btn-delete-confirm').on('click', function() {
                const providerId = $('#dataModal').data('provider-id');
                $.ajax({
                    url: '../controller/ServiceController.php?action=delete',
                    type: 'POST',
                    data: { id: providerId },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                showAlert('Sikeresen törölted a szolgáltatást!', 'success');
                                $('#deleteModal').modal('hide');
                                // Optionally reload after a short delay
                                setTimeout(() => location.reload(), 800);
                            } else {
                                showAlert('Hiba történt a törlés közben.', 'error');
                            }
                        } catch (e) {
                            showAlert('Hiba történt a törlés közben.', 'error');
                        }
                    },
                    error: function() {
                        showAlert('Hiba történt a törlés közben.', 'error');
                    }
                });
            });

            $('#deleteServiceModal .btn-delete-service-confirm').on('click', function() {
                const providerId = $(this).data('id');
                $.ajax({
                    url: '../controller/ServiceController.php?action=delete',
                    type: 'POST',
                    data: { id: providerId },
                    success: function(response) {
                        try {
                            const result = JSON.parse(response);
                            if (result.status === 'success') {
                                showAlert('Sikeresen törölted a szolgáltatást!', 'success');
                                location.reload();
                            } else {
                                showAlert('Hiba történt a törlés közben.', 'error');
                            }
                        } catch (e) {
                            showAlert('Hiba történt a törlés közben.', 'error');
                        }
                    },
                    error: function() {
                        showAlert('Hiba történt a törlés közben.', 'error');
                    }
                });
            });
        }

        function fetchProviderDetails(providerId) {
            console.log('Fetching provider details for ID:', providerId);
            $.ajax({
                url: '../controller/providerController.php?action=fetch',
                type: 'POST',
                data: { id: providerId },
                success: function(response) {
                    try {
                        const provider = typeof response === 'string' ? JSON.parse(response) : response;
                        
                        // Create HTML template using your existing CSS classes
                        const providerHtml = `
                            <div class="provider-container">
                                <div class="provider-header">
                                    <h3>${provider.name}</h3>
                                    <span class="provider-type">${provider.type}</span>
                                </div>
                                <div class="provider-body">
                                    <div class="provider-info">
                                        <p class="description">${provider.description}</p>
                                        <div class="info-item">
                                            <i class="fas fa-map-marker-alt"></i>
                                            <span>${provider.address}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-clock"></i>
                                            <span>${provider.working_hours}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>${provider.phone_number}</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-tag"></i>
                                            <span>${provider.price} Ft</span>
                                        </div>
                                        <div class="info-item">
                                            <i class="fas fa-hourglass-half"></i>
                                            <span>${provider.duration} perc</span>
                                        </div>
                                    </div>
                                </div>
                            </div>`;
                        
                        $('#modalBody').html(providerHtml);

                        // Check if current user is owner
                        const userId = $('body').data('user-id');
                        if (userId && provider.user_id == userId) {
                            $('#adminServiceActions').show();
                        } else {
                            $('#adminServiceActions').hide();
                        }
                    } catch (error) {
                        console.error('Error parsing provider details:', error);
                        showAlert('Error loading provider details', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('Error fetching provider details:', error);
                    showAlert('Error loading provider details', 'error');
                }
            });
        }

        // Update the initializeCalendar function
        function initializeCalendar() {
            const providerId = $('#dataModal').data('provider-id');
            
            // First fetch provider details to get duration
            $.ajax({
                url: '../controller/providerController.php?action=fetch',
                type: 'POST',
                data: { id: providerId },
                success: function(providerResponse) {
                    let provider = typeof providerResponse === 'string' 
                        ? JSON.parse(providerResponse) 
                        : providerResponse;

                    const providerDuration = parseInt(provider.duration) || 30;

                    // Now fetch working hours
                    $.ajax({
                        url: '../controller/providerController.php?action=getWorkingHours',
                        type: 'POST',
                        data: { provider_id: providerId },
                        success: function(response) {
                            try {
                                const workingHours = JSON.parse(response);
                                const [days, hours] = workingHours.working_hours.split(' ');
                                const [startDay, endDay] = days.split('-');
                                const [startTime, endTime] = hours.split('-');

                                // Define valid days mapping
                                const dayMapping = {
                                    'Hétfő': 1,
                                    'Kedd': 2,
                                    'Szerda': 3,
                                    'Csütörtök': 4,
                                    'Péntek': 5,
                                    'Szombat': 6,
                                    'Vasárnap': 0
                                };

                                // Get start and end day numbers
                                const startDayNum = dayMapping[startDay];
                                const endDayNum = dayMapping[endDay];

                                // Create array of valid days
                                const validDays = [];
                                let currentDay = startDayNum;
                                while (true) {
                                    validDays.push(currentDay);
                                    if (currentDay === endDayNum) break;
                                    currentDay = (currentDay % 7) + 1;
                                    if (currentDay === 0) currentDay = 7;
                                }

                                calendar = $('#calendar').fullCalendar({
                                    header: {
                                        left: 'prev,next today',
                                        center: 'title',
                                        right: 'month'
                                    },
                                    defaultView: 'month',
                                    height: 'auto',
                                    contentHeight: 'auto',
                                    selectable: true,
                                    selectHelper: true,
                                    locale: 'hu',
                                    firstDay: 1,
                                    monthNames: ['Január', 'Február', 'Március', 'Április', 'Május', 'Június', 
                                               'Július', 'Augusztus', 'Szeptember', 'Október', 'November', 'December'],
                                    monthNamesShort: ['Jan', 'Feb', 'Már', 'Ápr', 'Máj', 'Jún', 
                                                    'Júl', 'Aug', 'Szep', 'Okt', 'Nov', 'Dec'],
                                    dayNames: ['Vasárnap', 'Hétfő', 'Kedd', 'Szerda', 'Csütörtök', 'Péntek', 'Szombat'],
                                    dayNamesShort: ['Vas', 'Hét', 'Ke', 'Sze', 'Csü', 'Pén', 'Szo'],
                                    buttonText: {
                                        today: 'Ma',
                                        month: 'Hónap'
                                    },
                                    businessHours: {
                                        dow: validDays,
                                        start: startTime,
                                        end: endTime
                                    },
                                    selectConstraint: 'businessHours',
                                    dayRender: function(date, cell) {
                                        const dayOfWeek = date.day();
                                        if (!validDays.includes(dayOfWeek) || date.isBefore(moment(), 'day')) {
                                            cell.addClass('fc-disabled-day');
                                            cell.css({
                                                'background-color': '#f5f5f5',
                                                'opacity': '0.6',
                                                'cursor': 'not-allowed'
                                            });
                                        }
                                    },
                                    dayClick: function(date) {
                                        const dayOfWeek = date.day();
                                        if (!validDays.includes(dayOfWeek) || date.isBefore(moment(), 'day')) {
                                            return;
                                        }
                                        generateTimeSlots(date, startTime, endTime, providerDuration, providerId);
                                    },
                                    viewRender: function(view) {
                                        if (view.name === 'agendaDay') {
                                            $('.fc-prev-button, .fc-next-button, .fc-today-button').hide();
                                            $('.fc-month-button')
                                                .show()
                                                .text('Vissza');
                                            $('.fc-day-grid').hide();
                                            $('.fc-divider').hide();
                                        } else {
                                            $('.fc-prev-button, .fc-next-button, .fc-today-button').show();
                                            $('.fc-month-button').hide();
                                            $('.fc-day-grid').show();
                                            $('.fc-divider').show();
                                        }
                                    }
                                });
                            } catch (error) {
                                console.error('Error parsing working hours:', error);
                                showAlert('Hiba történt a nyitvatartási idő betöltésekor', 'error');
                            }
                        },
                        error: function(xhr, status, error) {
                            console.error('Error fetching working hours:', error);
                            showAlert('Hiba történt a nyitvatartási idő lekérdezésekor', 'error');
                        }
                    });
                }
            });
        }

        // Add this new function to fetch booked appointments
        function fetchBookedAppointments(date, providerId) {
            $.ajax({
                url: '../controller/providerController.php?action=getBookedAppointments',
                type: 'POST',
                data: {
                    date: date.format('YYYY-MM-DD'),
                    provider_id: providerId
                },
                success: function(response) {
                    const bookedSlots = JSON.parse(response);
                    calendar.fullCalendar('removeEvents');
                    calendar.fullCalendar('addEventSource', bookedSlots.map(slot => ({
                        start: moment(slot.appointment_date).format('YYYY-MM-DD HH:mm'),
                        end: moment(slot.appointment_date).add(30, 'minutes').format('YYYY-MM-DD HH:mm'),
                        title: 'Foglalt',
                        color: '#ff0000',
                        overlap: false
                    })));
                }
            });
        }

        function generateTimeSlots(date, startTime, endTime, duration, providerId) {
            const start = moment(date.format('YYYY-MM-DD') + ' ' + startTime);
            const end = moment(date.format('YYYY-MM-DD') + ' ' + endTime);
            
            // Clear previous time slots
            $('#calendar').fullCalendar('changeView', 'agendaDay', date);
            $('.fc-time-grid-container').empty().append('<div class="time-slots-container"></div>');
            
            const now = moment();
            const isToday = date.isSame(now, 'day');

            const timeSlots = [];
            let currentTime = start.clone();
            
            while (currentTime.isBefore(end)) {
                const slotEnd = currentTime.clone().add(duration, 'minutes');
                if (slotEnd.isAfter(end)) break;

                let isPast = false;
                if (isToday && currentTime.isBefore(now, 'minute')) {
                    isPast = true;
                }
                
                timeSlots.push({
                    start: currentTime.clone(),
                    end: slotEnd,
                    isPast: isPast
                });
                
                currentTime.add(duration, 'minutes');
            }
            
            // Fetch booked appointments to check availability
            $.ajax({
                url: '../controller/providerController.php?action=getBookedAppointments',
                type: 'POST',
                data: {
                    date: date.format('YYYY-MM-DD'),
                    provider_id: providerId
                },
                success: function(response) {
                    const bookedSlots = JSON.parse(response);
                    
                    timeSlots.forEach(slot => {
                        const isBooked = bookedSlots.some(booked => 
                            moment(booked.appointment_date).isBetween(
                                slot.start, 
                                slot.end, 
                                null, 
                                '[)'
                            )
                        );
                        
                        const timeSlotElement = $('<div>', {
                            class: 'time-slot' +
                                (isBooked ? ' booked' : '') +
                                (slot.isPast ? ' disabled' : ''),
                            text: `${slot.start.format('HH:mm')} - ${slot.end.format('HH:mm')}`,
                            data: {
                                start: slot.start.format('YYYY-MM-DD HH:mm'),
                                end: slot.end.format('YYYY-MM-DD HH:mm')
                            }
                        });
                        
                        if (!isBooked && !slot.isPast) {
                            timeSlotElement.click(function() {
                                $('.time-slot').removeClass('selected');
                                $(this).addClass('selected');
                                
                                $('#dataModal').data('selected-date', slot.start.format('YYYY-MM-DD'));
                                $('#dataModal').data('selected-time', slot.start.format('HH:mm'));
                            });
                        } else {
                            timeSlotElement.css({
                                'background-color': '#eee',
                                'color': '#aaa',
                                'cursor': 'not-allowed'
                            });
                        }
                        
                        $('.time-slots-container').append(timeSlotElement);
                    });
                }
            });
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
                    try {
                        const events = JSON.parse(response);
                        if (!Array.isArray(events)) {
                            throw new Error('Invalid response format');
                        }
                        calendar.fullCalendar('removeEvents');
                        calendar.fullCalendar('addEventSource', events);
                    } catch (error) {
                        console.error('Error parsing working hours:', error);
                        showAlert('Error loading available hours', 'error');
                    }
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