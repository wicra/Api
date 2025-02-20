<?php
require '../db/db.php';
header("Content-Type: application/json");

try {
    $query = "SELECT * FROM ponts ORDER BY pont_id ASC";
    $stmt = $conn->query($query);
    $ponts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "ponts" => $ponts]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>