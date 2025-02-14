<?php
require_once '../database.php';
require_once '../models/Provider.php';
session_start();

if (!isset($_SESSION['user']) || !isset($_SESSION['user']['role']) || $_SESSION['user']['role'] === 'customer') {
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit();
}

$action = $_GET['action'] ?? '';

if ($action === 'add') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        try {
            // Handle image upload
            $targetDir = "../uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $imageFile = $_FILES['image'];
            $imageFileType = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $imageFileType;
            $targetFile = $targetDir . $newFileName;
            
            // Store the relative path that will be used in the img src
            $relativePath = '/uploads/' . $newFileName;  // This is the path we'll store in database

            // Check if image file is valid
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $validExtensions)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            if (move_uploaded_file($imageFile['tmp_name'], $targetFile)) {
                // Insert provider data including image_path
                $stmt = $db->prepare("INSERT INTO providers (user_id, type, description, name, working_hours, address, phone_number, price, duration, image_path) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
                
                $userId = $_SESSION['user']['id'];
                $phoneNumber = $_SESSION['user']['phone'];
                
                $result = $stmt->execute([
                    $userId,
                    $_POST['type'],
                    $_POST['description'],
                    $_POST['name'],
                    $_POST['working_hours'],
                    $_POST['address'],
                    $phoneNumber,
                    $_POST['price'],
                    $_POST['duration'],
                    $relativePath  // Add the image path to the database
                ]);

                if ($result) {
                    echo json_encode([
                        'status' => 'success',
                        'message' => 'Service added successfully'
                    ]);
                } else {
                    throw new Exception('Failed to add service to database');
                }
            } else {
                throw new Exception('Failed to upload image');
            }
        } catch (Exception $e) {
            echo json_encode([
                'status' => 'error',
                'message' => $e->getMessage()
            ]);
        }
    }
}
?>