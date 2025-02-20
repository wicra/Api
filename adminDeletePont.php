<?php
require 'db.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['pont_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètre 'pont_id' manquant"]);
    exit();
}

$pont_id = intval($data['pont_id']);

try {
    $query = "DELETE FROM ponts WHERE pont_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id]);
    echo json_encode(["success" => true, "message" => "Pont supprimé avec succès"]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>