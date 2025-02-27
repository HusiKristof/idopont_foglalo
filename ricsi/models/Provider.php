<?php
class Provider {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function getProvidersByCategory($category) {
        $stmt = $this->db->prepare("SELECT p.*, COALESCE(AVG(r.rating), 0) as average_rating 
                                     FROM providers p 
                                     LEFT JOIN ratings r ON p.id = r.provider_id 
                                     WHERE p.categories = ?
                                     GROUP BY p.id");
        $stmt->execute([$category]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getProviderById($id) {
        $stmt = $this->db->prepare("SELECT * FROM providers WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}
?>