function fetchProvidersByCategory(category) {
    $.ajax({
        url: '../controller/ProviderController.php?action=filterByCategory',
        type: 'POST',
        data: { category: category },
        success: function(response) {
            const providers = JSON.parse(response);
            displayProviders(providers);
        },
        error: function(xhr, status, error) {
            console.error('Error fetching providers:', error);
        }
    });
}

function displayProviders(providers) {
    const providerList = document.getElementById('provider-list');
    providerList.innerHTML = ''; // Töröljük a meglévő elemeket

    providers.forEach(provider => {
        const providerDiv = document.createElement('div');
        providerDiv.className = 'provider-item col-lg-4 col-md-6 col-sm-12 mb-4';
        providerDiv.innerHTML = `
            <div class="card" data-id="${provider.id}">
                <img src="${provider.image_path || 'https://via.placeholder.com/300'}" alt="${provider.name}">
                <div class="card-footer">
                    <span>${provider.name}</span>
                    <span class="star">
                        <i class="fa fa-star"></i>
                        <span>${provider.average_rating.toFixed(1)}</span>
                    </span>
                </div>
            </div>`;
        providerList.appendChild(providerDiv);
    });
}

document.addEventListener("DOMContentLoaded", function () {
    const filterButtons = document.querySelectorAll(".filter-button");

    filterButtons.forEach(button => {
        button.addEventListener("click", function () {
            const category = this.getAttribute("data-category");
            fetchProvidersByCategory(category);
        });
    });
});