<?php
class User {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function register($name, $email, $phone, $password) {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $phone, $hashedPassword]);
    }

    public function login($email, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }
    
}
?>