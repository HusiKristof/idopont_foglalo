$(document).ready(function() {
    let userId;
    let calendar;

    //userId beállítása
    if (typeof window.userId !== 'undefined') {
        userId = window.userId;
    } else {
        userId = $('body').data('user-id');
    }
    console.log('User ID (booking.js):', userId);

    //kartya kattintás kezelése
    $('.card').on('click', function() {
        const providerId = $(this).data('id'); //provider ID lekérése kattintáskor
        $('#dataModal').data('provider-id', providerId); //provider ID beállítása a modalban
        
        if (!userId) {
            console.error('userId is not set properly');
        }
        
        $('#dataModal').modal('show');
    });

    (function() {
        function initializeEventHandlers() {
            console.log('Initializing event handlers');
            
            //kártya kattintás kezelése provider ID lekérése
            $('.card').on('click', function() {
                const providerId = $(this).data('id');
                console.log('Card clicked, provider ID:', providerId);
                $('#dataModal').data('provider-id', providerId);
                
                //fetch provider details
                fetchProviderDetails(providerId);
                
                //modal megjelenítése
                $('#dataModal').modal('show');
            });
        
            //book gomb kattintás kezelése
            $('#book').on('click', function() {
                console.log('Book button clicked');
                
                $('#modalBody').empty().append('<div id="calendar"></div>');
                
                $('.provider-details').hide();
                
                initializeCalendar();
                
                //booking button megjelenítése
                $('#bookAppointment').show();
                $(this).hide();
            });
        
            //booking button kattintás kezelése
            $('#bookAppointment').on('click', handleBooking);
        
            //modal bezárásakor reset view
            $('#dataModal').on('hidden.bs.modal', function() {
                console.log('Modal hidden - resetting view');
                // Reset buttons
                $('#book').show();
                $('#bookAppointment').hide();
                
                if (calendar) {
                    calendar.fullCalendar('destroy');
                    calendar = null;
                }
            });

            $('#editServiceBtn').on('click', function() {
                const providerId = $('#dataModal').data('provider-id');
                $.ajax({
                    url: '../controller/providerController.php?action=fetch',
                    type: 'POST',
                    data: { id: providerId },
                    success: function(providerResponse) {
                        const provider = typeof providerResponse === 'string'
                            ? JSON.parse(providerResponse)
                            : providerResponse;

                        $('#serviceType').val(provider.type);
                        $('#serviceName').val(provider.name);
                        $('#serviceDescription').val(provider.description);
                        $('#serviceWorkingHours').val(provider.working_hours);
                        $('#serviceAddress').val(provider.address);
                        $('#servicePrice').val(provider.price);
                        $('#serviceDuration').val(provider.duration);
                        $('#servicePhone').val(provider.phone_number || '');

                        $('#serviceImage').prop('disabled', true).closest('.mb-3').hide();

                        $('#addServiceForm').data('edit-id', providerId);

                        $('#saveService').text('Mentés (Szerkesztés)');

                        $('#addServiceModal').modal('show');
                    }
                });
            });

            $('#deleteServiceBtn').on('click', function() {
                $('#dataModal').modal('hide');
                setTimeout(function() {
                    $('#deleteModal').modal('show');
                }, 400); //animacios időzítése
            });

            //ha a deleteModal bezárul, akkor a dataModal is bezáruljon
            $('#deleteModal').on('hidden.bs.modal', function() {
                if (!$('#dataModal').hasClass('show')) {
                    setTimeout(function() {
                        $('#dataModal').modal('show');
                    }, 200);
                }
            });

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

                        //jelenlegi felhasználó ID lekérése - tulajdonos-e a szolgáltatásnak
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

        //initializecalendar function updatelese
        function initializeCalendar() {
            const providerId = $('#dataModal').data('provider-id');
            
            //provider id fetch
            $.ajax({
                url: '../controller/providerController.php?action=fetch',
                type: 'POST',
                data: { id: providerId },
                success: function(providerResponse) {
                    let provider = typeof providerResponse === 'string' 
                        ? JSON.parse(providerResponse) 
                        : providerResponse;

                    const providerDuration = parseInt(provider.duration) || 30;

                    //workinghours fetch
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

                                //napok mappolasa
                                const dayMapping = {
                                    'Hétfő': 1,
                                    'Kedd': 2,
                                    'Szerda': 3,
                                    'Csütörtök': 4,
                                    'Péntek': 5,
                                    'Szombat': 6,
                                    'Vasárnap': 0
                                };

                                //napok kezdete es vege
                                const startDayNum = dayMapping[startDay];
                                const endDayNum = dayMapping[endDay];

                                //tomb a valid napokkal
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
            
            //elozo time slotok torlese
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
            
            //foglalva van-e az idopont
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