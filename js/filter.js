$(document).ready(function() {
    function performSearch() {
        const query = $('#provider-search').val().trim();
        if (query === '') {
            // If empty, reload to restore paginated view
            window.location.href = window.location.pathname;
            return;
        }
        $.ajax({
            url: '../controller/ProviderController.php?action=search',
            type: 'POST',
            data: { query: query },
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        updateProviderList(result.providers);
                        $('.pagination').hide();
                    } else {
                        $('#provider-list').html('<div class="text-center w-100 mt-4">Nincs találat.</div>');
                        $('.pagination').hide();
                    }
                } catch (e) {
                    $('#provider-list').html('<div class="text-center w-100 mt-4">Hiba történt a keresés során.</div>');
                    $('.pagination').hide();
                }
            },
            error: function() {
                $('#provider-list').html('<div class="text-center w-100 mt-4">Hiba történt a keresés során.</div>');
                $('.pagination').hide();
            }
        });
    }

    // Search on Enter
    $('#provider-search').on('keydown', function(e) {
        if (e.key === 'Enter') {
            performSearch();
        }
    });

    // Search on icon click
    $('#provider-search-icon').on('click', function() {
        performSearch();
    });

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
                        $('.pagination').hide();
                        $('.base-providers').hide();
                        // Re-initialize lazy loading
                        $('.lazy').Lazy();
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
        let html = '';
        providers.forEach(function(provider) {
            html += `
                <div class="col-lg-4 col-md-6 col-sm-12 mb-4 provider-item" data-category="${provider.type}">
                    <div class="card" data-id="${provider.id}">
                        <img class="lazy" data-src="${provider.image_path || 'https://via.placeholder.com/300'}" alt="${provider.name}">
                        <div class="card-footer">
                            <span>${provider.name}</span>
                            <span class="star">
                                <i class="fa fa-star"></i>
                                <span>${parseFloat(provider.average_rating).toFixed(1)}</span>
                            </span>
                        </div>
                    </div>
                </div>
            `;
        });
        $('#provider-list').html(html);
        $('.lazy').Lazy();
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

    // Use event delegation for dynamically loaded provider cards
    $('#provider-list').off('click').on('click', '.card', function() {
        const providerId = $(this).data('id');
        // Fetch and show provider details as before
        fetchProviderDetails(providerId);
        $('#dataModal').data('provider-id', providerId).modal('show');
    });

    // Alapértelmezés szerint csak az alap szolgáltatók jelenjenek meg
    $('.provider-item').hide();
});