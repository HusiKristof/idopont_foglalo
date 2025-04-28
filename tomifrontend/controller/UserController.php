<?php
require_once '../models/User.php';
require_once '../database.php'; // Database connection
session_start();

header('Content-Type: application/json');

$action = isset($_GET["action"]) ? $_GET["action"] : null;

$userModel = new User($db);

if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $userModel->login($data['email'], $data['password']);
        
        if ($result['status'] === 'success') {
            $_SESSION['user'] = $result['user'];
        }
        
        echo json_encode($result);
    }
} elseif ($action === 'register') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $result = $userModel->register(
            $data['name'],
            $data['email'], 
            $data['phone'],
            $data['password']
        );
        echo json_encode($result);
        exit;
    }
}
// Remove or comment out any other registration logic below this point!
?>