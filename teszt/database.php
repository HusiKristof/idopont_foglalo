<?php
$host = 'localhost';  // localhost helyett 127.0.0.1
$port = 8889;         // MAMP MySQL alapértelmezett portja
$dbName = 'vizsga';
$user = 'root';
$pass = 'root';

/* try {
    // PDO objektum létrehozása
    $db = new PDO("mysql:host=$host;port=$port;dbname=$dbName;charset=utf8mb4", $user, $pass);

    // HIBA: $db helyett $pdo-t kellene használni
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Kapcsolódás sikertelen: " . $e->getMessage();
    exit();
} */

class Config {

    private $dbCon;

    public function __construct()
    {
        $this->dbCon = mysqli_connect('localhost', 'root', 'root', 'vizsga');
    }

    public function getConnection(){
        return $this->dbCon;
    }

    public function close(){
        mysqli_close($this->dbCon);
        $this->dbCon = null;
    }
}
?>
