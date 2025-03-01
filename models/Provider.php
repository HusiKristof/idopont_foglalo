<?php
class Provider {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getProviderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM providers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>