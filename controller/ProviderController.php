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
        } else {
            echo 'Provider not found.';
        }
    } else {
        echo 'Invalid request.';
    }
}

// Ellenőrizzük, hogy a user_id benne van-e a session-ben
$user_id = $_SESSION['user']['id'] ?? null;
if (!$user_id) {
    echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['selectedDate'] ?? '';
    $provider_id = $_POST['provider_id'] ?? '';

    if ($date && $provider_id) {
        $stmt = $db->prepare("INSERT INTO appointments (user_id, service_id, provider_id, appointment_date, status) VALUES (?, ?, ?, ?, 'pending')");
        
        if ($stmt->execute([$user_id, $service_id, $provider_id, $date,])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => $stmt->errorInfo()]);
        }
    } else {
        //echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}

?>