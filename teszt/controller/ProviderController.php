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
    $user_id = $_SESSION['user']['id'] ?? null;
    if (!$user_id) {
        echo json_encode(['status' => 'error', 'message' => 'User ID not found in session.']);
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Log the incoming data
        error_log('Booking request data: ' . print_r($_POST, true));
        
        $date = $_POST['selectedDate'] ?? null;
        $provider_id = $_POST['provider_id'] ?? null;
        
        if ($date && $provider_id) {
            // Remove the service_id from the query since it's not being used
            $stmt = $db->prepare("INSERT INTO appointments (user_id, provider_id, appointment_date, status) VALUES (?, ?, ?, 'pending')");
            if ($stmt->execute([$user_id, $provider_id, $date])) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => $stmt->errorInfo()]);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
        }
    }
}
?>