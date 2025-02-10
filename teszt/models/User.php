<?php

require_once '../database.php';

class User {
    private $db;
    private $id;
    private $name;
    private $email;
    private $phone;
    private $password;

    public function __construct($database, $id, $name, $email, $phone, $password) {
        $this->db = $database;
        $this->id = $id;
        $this->name = $name;
        $this->email = $email;
        $this->phone = $phone;
        $this->password = $password;
    }

    public function getId() {return $this->id;}

	public function getName(): mixed {return $this->name;}

	public function getEmail(): mixed {return $this->email;}

    public function getPhone(): mixed {return $this->phone;}

	public function getPassword(): mixed {return $this->password;}

	public function setId( $id): void {$this->id = $id;}

	public function setName( $name): void {$this->name = $name;}

	public function setEmail( $email): void {$this->email = $email;}

    public function setPhone( $phone): void {$this->phone = $phone;}

	public function setPassword( $password): void {$this->password = $password;}

    public static function registerUser (User $user){
        $name = $user->getName();
        $email = $user->getEmail();
        $phone = $user->getPhone();
        $password = $user->getPassword();
    
        $config = new Config();
        $connection = $config->getConnection();
        
        // Check if email exists
        /* $stmt = $connection->prepare("SELECT COUNT(*) FROM users WHERE email = ?");
        $stmt->execute([$email]);
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("Email already registered!");
        } */
        
        // Insert new user
        $stmt = $connection->prepare("INSERT INTO users (name, email, phone, password) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$name, $email, $phone, password_hash($password, PASSWORD_DEFAULT)]);
    }


    public static function loginUser(User $user, $email, $password) {

        global $db;
        $email = $user->getEmail();
        $password = $user->getPassword();

        $stmt = $db->prepare("SELECT id, name, email, phone, password FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user; // A felhasználó adatai, beleértve az id-t is
        }

        
        header('Location: ../views/mainpage.php');

        return false;
    }
}
?>