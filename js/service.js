$(document).ready(function() {
    // Handle service form submission
    $('#saveService').on('click', function() {
        const form = document.getElementById('addServiceForm');
        const formData = new FormData(form);
        
        $.ajax({
            url: '../controller/ServiceController.php?action=add',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                try {
                    const result = JSON.parse(response);
                    if (result.status === 'success') {
                        showAlert('Sikeresen hozzáadtad a szolgáltatásod!', 'success');
                        location.reload();
                    } else {
                        alert('Error: ' + result.message);
                    }
                } catch (e) {
                    alert('Error processing response');
                }
            },
            error: function(xhr, status, error) {
                alert('Error adding service: ' + error);
            }
        });
    });
});