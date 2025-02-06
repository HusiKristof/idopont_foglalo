<?php
class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function register($name, $email, $phone, $password) {
        global $db; // Assuming you have a global PDO connection
    
        // Check if email exists
        $stmt = $db->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already registered!");
        }
    
        // Insert new user
        $stmt = $db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT id, name, email, phone, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // A felhasználó adatai, beleértve az id-t is
        }
        return false;
    }
}
?>