<?php
class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function login($email, $password) {
        try {
            $stmt = $this->db->prepare("SELECT id, name, email, phone, password, role FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                unset($user['password']);
                return [
                    'status' => 'success',
                    'user' => $user
                ];
            }
            
            // Add logging
            error_log("Failed login attempt for email: " . $email);
            
            return [
                'status' => 'error',
                'message' => 'Invalid credentials'
            ];
        } catch (PDOException $e) {
            error_log("Database error during login: " . $e->getMessage());
            return [
                'status' => 'error',
                'message' => 'An error occurred during login'
            ];
        }
    }

    public function register($name, $email, $phone, $password) {
        try {
            // Check if email already exists
            if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                return [
                    'status' => 'error',
                    'message' => 'Érvénytelen email cím formátum.'
                ];
            }
            $stmt = $this->db->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);
            if ($stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Ez az email cím már foglalt.'
                ];
            }

            // Check if phone already exists
            $stmt = $this->db->prepare("SELECT id FROM users WHERE phone = ?");
            $stmt->execute([$phone]);
            if ($stmt->fetch()) {
                return [
                    'status' => 'error',
                    'message' => 'Ez a telefonszám már foglalt.'
                ];
            }

            $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, password, role) VALUES (?, ?, ?, ?, 'customer')");
            
            if ($stmt->execute([$name, $email, $phone, $hashedPassword])) {
                return [
                    'status' => 'success',
                    'message' => 'Registration successful'
                ];
            }
            return [
                'status' => 'error',
                'message' => 'Registration failed'
            ];
        } catch (PDOException $e) {
            return [
                'status' => 'error',
                'message' => 'Database error: ' . $e->getMessage()
            ];
        }
    }
}
?>