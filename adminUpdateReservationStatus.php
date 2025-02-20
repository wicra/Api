<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['reservation_id']) ||
    empty($data['user_id']) ||
    empty($data['statut'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

$reservation_id = intval($data['reservation_id']);
$user_id = intval($data['user_id']);
$statut = $data['statut'];

// Pour un utilisateur non admin, seuls "en attente" et "annulé" sont autorisés
$allowed = ["en attente", "annulé", "confirmé", "maintenance"];
if (!in_array($statut, $allowed)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

try {
    $query = "UPDATE reservations SET statut = ? WHERE reservation_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$statut, $reservation_id, $user_id]);
    echo json_encode(["success" => true, "message" => "Mise à jour effectuée"]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
exit();
?>