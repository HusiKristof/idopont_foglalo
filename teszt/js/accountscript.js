function toggleEditForm() {
    var editForm = document.getElementById('edit-form');
    var accountDetails = document.getElementById('account-details');
    
    if (editForm.style.display === 'none') {
        editForm.style.display = 'block';
        accountDetails.style.display = 'none'; // Elrejti az alap adatokat
    } else {
        editForm.style.display = 'none';
        accountDetails.style.display = 'block'; // Visszaállítja az alap adatokat
    }
}