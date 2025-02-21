<?php
require '../db/db.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['reservation_id']) ||
    empty($data['statut'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

$reservation_id = intval($data['reservation_id']);
$statut = $data['statut'];

// Liste des statuts autorisés
$allowed = ["confirmé", "annulé", "en attente", "maintenance"];
if (!in_array($statut, $allowed)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

try {
    $query = "UPDATE reservations SET statut = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$statut, $reservation_id]);
    echo json_encode(["success" => true, "message" => "Statut mis à jour"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>