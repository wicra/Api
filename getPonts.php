<?php
require 'db.php';

try {
    $stmt = $conn->query("SELECT pont_id, nom FROM ponts");
    $ponts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "ponts" => $ponts]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>