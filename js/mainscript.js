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
            left: 'prev,next today',
            center: 'title',
            right: 'month,agendaWeek,agendaDay'
          },
          selectable: true,
          selectConstraint: {
            start: moment().startOf('day'),
            end: moment().add(3, 'months').endOf('day')
          },
          select: handleDateSelect,
          eventRender: function(event, element) {
            element.find('.fc-title').append(
              `<br/><small>${event.provider_name || ''}</small>`
            );
          }
        });
        console.log('Calendar initialized successfully');
      } catch (error) {
        console.error('Error initializing calendar:', error);
      }
    }
  
    function handleDateSelect(start, end) {
      console.log('Date selected:', start.format('YYYY-MM-DD'));
      if (!userId) {
        alert('Please log in to book appointments');
        return;
      }
  
      const selectedDate = start.format('YYYY-MM-DD');
      const providerId = $('#dataModal').data('provider-id');
      
      if (!providerId) {
        alert('Please select a provider first');
        return;
      }
  
    }
  
    function handleBooking() {
      console.log('Booking button clicked');
      const selectedDate = calendar.fullCalendar('getDate').format('YYYY-MM-DD');
      const providerId = $('#dataModal').data('provider-id');
      
      if (!selectedDate || !providerId) {
        alert('Please select both a date and provider');
        return;
      }

      
      if (confirm('Leszeretnéd foglalni az időpontot erre a dátumra: ' + selectedDate + '?')) {
        createAppointment(selectedDate, providerId);
      }
    }
  
    function createAppointment(selectedDate, providerId) {
      console.log('Creating appointment for date:', selectedDate, 'provider:', providerId);
      $.ajax({
        url: '../controller/providerController.php?action=book',
        type: 'POST',
        data: {
            selectedDate: selectedDate,
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
        provider_id: providerId,
        user_id: userId
    });
    }
})();