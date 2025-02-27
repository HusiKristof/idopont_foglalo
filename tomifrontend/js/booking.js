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

        // Update the initializeCalendar function
        function initializeCalendar() {
            const providerId = $('#dataModal').data('provider-id');
            
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
                                if (!validDays.includes(dayOfWeek)) {
                                    cell.addClass('fc-disabled-day');
                                    cell.css({
                                        'background-color': '#f5f5f5',
                                        'opacity': '0.6',
                                        'cursor': 'not-allowed'
                                    });
                                }
                            },
                            // When a day is clicked in month view
                            dayClick: function(date) {
                                // First fetch booked appointments for this date
                                fetchBookedAppointments(date, providerId);
                                
                                calendar.fullCalendar('changeView', 'agendaDay', date);
                                // After switching to day view, apply these settings
                                calendar.fullCalendar('option', {
                                    slotDuration: '00:30:00',
                                    minTime: startTime,
                                    maxTime: endTime,
                                    allDaySlot: false,
                                    selectable: true,
                                    selectHelper: true,
                                    timeFormat: 'H:mm',
                                    slotEventOverlap: false,
                                    height: 'auto',
                                    contentHeight: 'auto',
                                    selectConstraint: {
                                        start: '00:00',
                                        end: '24:00',
                                        dow: [0, 1, 2, 3, 4, 5, 6]
                                    },
                                    selectOverlap: false,
                                    // Add unselect callback
                                    unselect: function(jsEvent, view) {
                                        // Prevent automatic unselect
                                        if (jsEvent) {
                                            jsEvent.preventDefault();
                                        }
                                    },
                                    // Update select callback
                                    select: function(start, end) {
                                        const dayOfWeek = start.day();
                                        if (!validDays.includes(dayOfWeek)) {
                                            calendar.fullCalendar('unselect');
                                            showAlert('Ez a nap nem tartozik a nyitvatartási időbe', 'error');
                                            return;
                                        }
                                        
                                        // Check for overlapping events
                                        const overlappingEvents = calendar.fullCalendar('clientEvents', function(event) {
                                            return (start.isBefore(event.end) && end.isAfter(event.start));
                                        });

                                        if (overlappingEvents.length === 0) {
                                            // Remove any existing selection first
                                            calendar.fullCalendar('removeEvents', function(evt) {
                                                return evt.className && evt.className.indexOf('selected-slot') !== -1;
                                            });

                                            // Create the new selection event
                                            const selectionEvent = {
                                                start: start,
                                                end: end,
                                                className: 'selected-slot',
                                                title: start.format('HH:mm') + '-' + end.format('HH:mm'),
                                                color: '#2196F3',
                                                overlap: false,
                                                editable: false,
                                                stick: true
                                            };

                                            // Render the event and make it stick
                                            calendar.fullCalendar('renderEvent', selectionEvent, true);

                                            // Store the selected date and time
                                            $('#dataModal').data('selected-date', start.format('YYYY-MM-DD'));
                                            $('#dataModal').data('selected-time', start.format('HH:mm'));

                                            // Optionally show a confirmation message
                                            showAlert(`Kiválasztott időpont: ${start.format('YYYY-MM-DD HH:mm')}`, 'success');
                                        } else {
                                            showAlert('The selected time slot overlaps with an existing booking.', 'error');
                                        }
                                    }
                                });
                            },
                            views: {
                                month: {
                                    titleFormat: 'YYYY. MMMM',
                                    columnHeaderFormat: 'dddd'
                                },
                                agendaDay: {
                                    titleFormat: 'YYYY. MMMM D.',
                                    columnHeaderFormat: 'dddd'
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