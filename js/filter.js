$(document).ready(function() {
    $('.filter-button').on('click', function() {
        const type = $(this).data('type');
        
        $.ajax({
            url: '../controller/ProviderController.php?action=filter_providers',
            type: 'POST',
            data: {
                type: type
            },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        updateProviderList(result.providers);
                        $('.base-providers').hide();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (e) {
                    alert('Error processing response');
                }
            },
            error: function(xhr, status, error) {
                alert('Error fetching providers: ' + error);
            }
        });
    });

    function updateProviderList(providers) {
        const providerList = $('#provider-list');
        providerList.empty();
        
        providers.forEach(provider => {
            const providerItem = `<div class="col-lg-4 col-md-6 col-sm-12 mb-4">
                <div class="card" data-id="${provider.id}">
                    <img src="${provider.image_path ? provider.image_path : 'https://via.placeholder.com/300'}" alt="${provider.name}">
                    <div class="card-footer">
                        <span>${provider.name}</span>
                        <span class="star">
                            <i class="fa fa-star"></i>
                            <span>${parseFloat(provider.average_rating).toFixed(1)}</span>
                        </span>
                    </div>
                </div>
            </div>`;
            providerList.append(providerItem);
        });
    
        // Re-attach the click event handler for the .card elements
        $('.card').on('click', function() {
            const providerId = $(this).data('id');
            $('#dataModal').data('provider-id', providerId);
            
            // Fetch and display provider details
            fetchProviderDetails(providerId);
            
            // Show the modal
            $('#dataModal').modal('show');
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

    // Alapértelmezés szerint csak az alap szolgáltatók jelenjenek meg
    $('.provider-item').hide();
});