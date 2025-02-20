<?php
require_once '../models/User.php';
require_once '../models/Appointment.php';
require_once '../database.php'; // Database connection

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}

header('Content-Type: application/json'); // Ensure the response is JSON

$action = isset($_GET["action"]) ? $_GET["action"] : null;
$userModel = new User($db);
$appointmentModel = new Appointment($db);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($action === 'save_rating') {
        $userId = $_POST['user_id'];
        $appointmentId = $_POST['appointment_id'];
        $providerId = $_POST['provider_id'];
        $rating = $_POST['rating'];

        if ($userId && $appointmentId && $providerId && $rating) {
            $stmt = $db->prepare("INSERT INTO ratings (user_id, appointment_id, provider_id, rating) VALUES (?, ?, ?, ?)");
            if ($stmt->execute([$userId, $appointmentId, $providerId, $rating])){
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save rating']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        }
    } elseif ($action === 'create_appointment') {
        $date = $_POST['appointment_date'] ?? '';
        $user_id = $_POST['user_id'] ?? '';
        $provider_id = $_POST['provider_id'] ?? '';

        if ($date && $user_id && $provider_id) {
            $stmt = $db->prepare("INSERT INTO appointments (user_id, appointment_date, provider_id, status) VALUES (?, ?, ?, 'pending')");
            if ($stmt->execute([$user_id, $date, $provider_id])) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Could not create appointment']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid action']);
    }
} elseif ($action === 'update_status') {
    $appointmentId = $_POST['appointment_id'];
    $status = $_POST['status'];

    $stmt = $db->prepare("UPDATE appointments SET status = ? WHERE id = ?");
    if ($stmt->execute([$status, $appointmentId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to update status']);
    }
} elseif ($action === 'delete_appointment') {
    $appointmentId = $_POST['appointment_id'];
    $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
    if ($stmt->execute([$appointmentId])) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Failed to delete appointment']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}
?>