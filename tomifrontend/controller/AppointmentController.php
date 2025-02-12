<?php
require_once '../database.php'; // Database connection

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $date = $_POST['appointment_date'] ?? '';
    $user_id = $_POST['user_id'] ?? '';
    $provider_id = $_POST['provider_id'] ?? '';

    if ($date && $user_id && $provider_id) {
        // SQL lekérdezés a foglalás beszúrására
        $stmt = $db->prepare("INSERT INTO appointments (user_id, appointment_date, provider_id, status) VALUES (?, ?, ?, 'pending')");
        if ($stmt->execute([$user_id, $date, $provider_id])) {
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
