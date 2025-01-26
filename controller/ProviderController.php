<?php
require_once '../models/Provider.php';
require_once '../database.php'; // Database connection

$action = $_GET['action'] ?? '';

$providerModel = new Provider($db);

if ($action === 'fetch') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $provider_id = (int)$_POST['id']; // Get the provider ID from the POST request
        $provider = $providerModel->getProviderById($provider_id);

        if ($provider) {
            // A szolgáltató részleteinek kiírása HTML formátumban
            echo '<div class="provider-details">';
            echo '<h5>' . htmlspecialchars($provider['name']) . '</h5>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($provider['description']) . '</p>';
            echo '<p><strong>Provider:</strong> ' . htmlspecialchars($provider['type']) . '</p>';
            echo '<p><strong>Price:</strong> ' . htmlspecialchars($provider['price']) . ' Ft</p>';
            echo '<p><strong>Duration:</strong> ' . htmlspecialchars($provider['duration']) . ' minutes</p>';
            echo '</div>'; // zárd le a provider-details div-et
        
            // Naptár HTML
            echo '<div class="calendar">';
            echo '<h6>Időpontfoglalás</h6>';
            echo '<div id="calendar"></div>';
            echo '</div>';
        } else {
            echo 'Provider not found.';
        }
    } else {
        echo 'Invalid request.';
    }
}
?>