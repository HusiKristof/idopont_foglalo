<?php
require_once '../models/User.php';
require_once '../database.php'; // Database connection

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php'); // Redirect to login if not logged in
    exit();
}


class Appointment {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function saveRating($userId, $appointmentId, $providerId, $serviceId, $rating) {
        $stmt = $this->db->prepare("INSERT INTO ratings (user_id, appointment_id, provider_id, service_id, rating) VALUES (?, ?, ?, ?, ?)");
        return $stmt->execute([$userId, $appointmentId, $providerId, $serviceId, $rating]);
    }
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



$action = isset($_GET["action"]) ? $_GET["action"] : null;
$userModel = new User($db);

$action = isset($_GET["action"]) ? $_GET["action"] : null;
$appointmentModel = new Appointment($db);


if ($action === 'save_rating') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $userId = $_POST['user_id'];
        $appointmentId = $_POST['appointment_id'];
        $providerId = $_POST['provider_id'];
        $serviceId = 1;// Ensure serviceId is handled
        $rating = $_POST['rating'];

        if ($userId && $appointmentId && $providerId && $rating) {
            if ($appointmentModel->saveRating($userId, $appointmentId, $providerId, $serviceId, $rating)) {
                echo json_encode(['status' => 'success']);
            } else {
                echo json_encode(['status' => 'error', 'message' => 'Failed to save rating']);
            }
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
        }
    }
}  elseif ($action === 'create_appointment') {
    // Existing create_appointment code
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