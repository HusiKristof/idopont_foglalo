<?php
require_once '../database.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['date'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $provider_id = $_POST['provider_id'] ?? '';

    if ($date && $user_id && $provider_id) {
        // Prepare and execute the SQL statement to insert the appointment
        $stmt = $db->prepare("INSERT INTO appointments (date, user_id, provider_id, status) VALUES (?, ?, ?, 'pending')");
        if ($stmt->execute([$date, $user_id, $provider_id])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Could not create appointment.']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method.']);
}
?>