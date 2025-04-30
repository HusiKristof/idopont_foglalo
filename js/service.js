$(document).ready(function() {
    $('#saveService').on('click', function() {
        const form = document.getElementById('addServiceForm');
        const formData = new FormData(form);

        const editId = $('#addServiceForm').data('edit-id');
        if (editId) {
            formData.append('id', editId);
            $.ajax({
                url: '../controller/ServiceController.php?action=edit',
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(response) {
                    try {
                        const result = JSON.parse(response);
                        if (result.status === 'success') {
                            showAlert('Sikeresen módosítottad a szolgáltatást!', 'success');
                            location.reload();
                        } else {
                            alert('Error: ' + result.message);
                        }
                    } catch (e) {
                        alert('Error processing response');
                    }
                },
                error: function(xhr, status, error) {
                    alert('Error editing service: ' + error);
                }
            });
        } else {
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
        }
    });
});