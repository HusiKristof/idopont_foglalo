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
            //img upload
            $targetDir = "../uploads/";
            if (!file_exists($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $imageFile = $_FILES['image'];
            $imageFileType = strtolower(pathinfo($imageFile['name'], PATHINFO_EXTENSION));
            $newFileName = uniqid() . '.' . $imageFileType;
            $targetFile = $targetDir . $newFileName;
            
            $relativePath = '/uploads/' . $newFileName;  //path az adatbázisba

            //kep validacio
            $validExtensions = ['jpg', 'jpeg', 'png', 'gif'];
            if (!in_array($imageFileType, $validExtensions)) {
                throw new Exception('Invalid file type. Only JPG, JPEG, PNG & GIF files are allowed.');
            }

            if (move_uploaded_file($imageFile['tmp_name'], $targetFile)) {
                //validacio a provider adatokra hozzaadas elott

                $workingHours = $_POST['working_hours'];
                if (!preg_match('/^(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)-(Hétfő|Kedd|Szerda|Csütörtök|Péntek|Szombat|Vasárnap)\s([01][0-9]|2[0-3]):[0-5][0-9]-([01][0-9]|2[0-3]):[0-5][0-9]$/', $workingHours)) {
                    echo json_encode([
                        'status' => 'error',
                        'message' => 'Invalid working hours format. Use format: Nap-Nap ÓÓ:PP-ÓÓ:PP'
                    ]);
                    exit;
                }

                //provider adatok beszúrása
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
                    $relativePath  //image path az adatbázisba
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

if ($action === 'edit') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'] ?? null;
        $userId = $_SESSION['user']['id'] ?? null;
        $type = $_POST['type'] ?? '';
        $description = $_POST['description'] ?? '';
        $name = $_POST['name'] ?? '';
        $working_hours = $_POST['working_hours'] ?? '';
        $address = $_POST['address'] ?? '';
        $phone_number = $_POST['phone_number'] ?? '';
        $price = $_POST['price'] ?? 0;
        $duration = $_POST['duration'] ?? 0;

        if (!$id || !$userId) {
            echo json_encode(['status' => 'error', 'message' => 'Missing ID or user']);
            exit;
        }

        $stmt = $db->prepare("UPDATE providers SET type=?, description=?, name=?, working_hours=?, address=?, phone_number=?, price=?, duration=? WHERE id=? AND user_id=?");
        $result = $stmt->execute([
            $type,
            $description,
            $name,
            $working_hours,
            $address,
            $phone_number,
            $price,
            $duration,
            $id,
            $userId
        ]);
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Service updated successfully' : 'Failed to update service'
        ]);
        exit;
    }
}

if ($action === 'delete') {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $id = $_POST['id'];
        $userId = $_SESSION['user']['id'];
        $stmt = $db->prepare("DELETE FROM providers WHERE id=? AND user_id=?");
        $result = $stmt->execute([$id, $userId]);
        echo json_encode([
            'status' => $result ? 'success' : 'error',
            'message' => $result ? 'Service deleted successfully' : 'Failed to delete service'
        ]);
    }
    exit;
}
?>