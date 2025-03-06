<?php
require_once '../models/User.php';
require_once '../database.php'; // Database connection
session_start();

$action = isset($_GET["action"]) ? $_GET["action"] : null;

$userModel = new User($db);

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = $userModel->login($email, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'role' => $user['role']  // Added role to session
            ];
            header('Location: ../views/mainpage.php');
            exit();
        } else {
            echo "Invalid credentials!";
        }
    }
} elseif ($action === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $phone = $_POST['phone'];
        $password = $_POST['password'];

        if ($userModel->register($name, $email, $phone, $password)) {
            header('Location: ../index.php');
            exit();
        } else {
            echo "Registration failed!";
        }
    }
}/*  elseif ($action === 'delete_appointment') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointmentId = $_POST['appointment_id'];
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        if ($stmt->execute([$appointmentId])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete appointment']);
        }
    }
} */
