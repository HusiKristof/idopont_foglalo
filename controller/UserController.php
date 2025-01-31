<?php
require_once '../models/User.php';
require_once '../database.php'; // Database connection

$action = $_GET['action'] ?? '';

$userModel = new User($db);

if ($action === 'login') {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user = $userModel->login($email, $password);
    if ($user) {
        session_start();
        $_SESSION['user'] = [
            'id' => $user['id'], // Az id érték beállítása
            'name' => $user['name'],
            'email' => $user['email'],
            'phone' => $user['phone']
        ];
        header('Location: ../views/explore.php');
        exit(); // Ne felejtsd el az exit() hívást az átirányítás után
    } else {
        echo "Invalid credentials!";
    }
} elseif ($action === 'register') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $password = $_POST['password'];

    if ($userModel->register($name, $email, $phone, $password)) {
        header('Location: ../views/index.php'); // Redirect to login page after registration
        exit(); // Ne felejtsd el az exit() hívást az átirányítás után
    } else {
        echo "Registration failed!";
    }
}
?>