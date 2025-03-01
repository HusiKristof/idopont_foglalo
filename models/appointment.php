<?php

class Appointment {
    private $db;

    public function __construct($database) {
        $this->db = $database;
    }

    public function saveRating($userId, $appointmentId, $providerId, $rating) {
        $stmt = $this->db->prepare("INSERT INTO ratings (user_id, appointment_id, provider_id, rating) VALUES (?, ?, ?, ?)");
        return $stmt->execute([$userId, $appointmentId, $providerId, $rating]);
    }
}

?>