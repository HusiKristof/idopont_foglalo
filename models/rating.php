<?php
$host = '127.0.0.1';  // localhost helyett 127.0.0.1
$port = 8889;         // MAMP MySQL alapértelmezett portja
$dbName = 'vizsga';
$user = 'root';
$pass = 'root';

class Rating {
    private $db;
    public function __construct($database) {
        $this->db = $database;
    }
    public function getAllRatings() {
        $stmt = $this->db->prepare("SELECT provider_id, AVG(rating) as average_rating FROM ratings GROUP BY provider_id");
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }    
    
}

// Create database connection without debug output
$database = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $user, $pass);
$rating = new Rating($database);
$ratings = $rating->getAllRatings();
?>