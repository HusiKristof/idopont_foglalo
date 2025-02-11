<?php
require_once '../models/User.php';
require_once '../database.php'; // Database connection
session_start();

session_start();
if (!isset($_SESSION['user'])) {
    header('Location: index.php');
    exit();
}

class UserController {
    static function registerUser($request){
        $userArray = $request->getBody();

        $user = new User($userArray['database'], $userArray['id'], $userArray['name'], $userArray['email'], $userArray['phone'], $userArray['password']);
        $response = UserService::register($user);

        return $response;
    }

    static function loginUser($request){
        $userArray = $request->getBody();

        $user = new User($userArray['database'], $userArray['id'], $userArray['name'], $userArray['email'], $userArray['phone'], $userArray['password']);
        $response = UserService::login($user);

        return $response;
    }
}
/* $action = isset($_GET["action"]) ? $_GET["action"] : null;

$userModel = new User($db);


if ($action === 'login') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $email = $_POST['email'];
        $password = $_POST['password'];
        $user = $userModel->login($email, $password);
        if ($user) {
            $_SESSION['user'] = [
                'id' => $user['id'], // Az id érték beállítása
                'name' => $user['name'],
                'email' => $user['email'],
                'phone' => $user['phone']
            ];
            header('Location: ../views/landing.php');
            exit(); // Ne felejtsd el az exit() hívást az átirányítás után
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

<<<<<<< Updated upstream:teszt/controller/UserController.php
    if ($userModel->register($name, $email, $phone, $password)) {
        header('Location: ../index.php'); // Redirect to login page after registration
        exit(); // Ne felejtsd el az exit() hívást az átirányítás után
    } else {
        echo "Registration failed!";
=======
        if ($userModel->register($name, $email, $phone, $password)) {
            header('Location: ../views/index.php'); // Redirect to login page after registration
            exit(); // Ne felejtsd el az exit() hívást az átirányítás után
        } else {
            echo "Registration failed!";
        }
    }
} elseif ($action === 'logout') {
    session_destroy();
    header('Location: ../views/login.php');
    exit();
} elseif ($action === 'delete_appointment') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $appointmentId = $_POST['appointment_id'];
        $stmt = $db->prepare("DELETE FROM appointments WHERE id = ?");
        if ($stmt->execute([$appointmentId])) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Failed to delete appointment']);
        }
>>>>>>> Stashed changes:tomifrontend/controller/UserController.php
    }
} */
?>