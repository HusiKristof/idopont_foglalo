<?php
require_once '../models/User.php';
require_once '../database.php'; 
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
                'role' => $user['role']  
            ];
            header('Location: ../views/mainpage.php'); 
            exit();
        } else {
            http_response_code(400); // Set the response code to 400
            $_SESSION['error'] = "Helytelen email vagy jelszó!";
            echo json_encode(['status' => 'error', 'message' => 'Helytelen email vagy jelszó!']);
            exit();
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
            echo "A regisztráció sikertelen!";
        }
    }
}
?>