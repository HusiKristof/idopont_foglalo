<?php
require_once '../models/Provider.php';
require_once '../database.php';
session_start();

if (!isset($_SESSION['user'])) {
    echo json_encode(['status' => 'error', 'message' => 'You must be logged in to make a booking.']);
    exit();
}

$action = $_GET['action'] ?? '';
$providerModel = new Provider($db);

if ($action === 'fetch') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
        $provider_id = (int)$_POST['id'];
        $provider = $providerModel->getProviderById($provider_id);
        if ($provider) {
            echo '<div class="provider-details">';
            echo '<h5>' . htmlspecialchars($provider['name']) . '</h5>';
            echo '<p><strong>Description:</strong> ' . htmlspecialchars($provider['description']) . '</p>';
            echo '<p><strong>Provider:</strong> ' . htmlspecialchars($provider['type']) . '</p>';
            echo '<p><strong>Price:</strong> ' . htmlspecialchars($provider['price']) . ' Ft</p>';
            echo '<p><strong>Duration:</strong> ' . htmlspecialchars($provider['duration']) . ' minutes</p>';
            echo '</div>';
        } else {
            echo 'Provider not found.';
        }
    }
} elseif ($action === 'book') {
    // Booking logic...
} elseif ($action === 'fetchHours') {
    // Fetch available hours logic...
} elseif ($action === 'filterByCategory') {
    $category = $_POST['category'] ?? '';
    $providers = $providerModel->getProvidersByCategory($category);
    echo json_encode($providers);
    exit();
}
?>