<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header('Content-Type: application/json');
require '../db/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['reservation_id']) && isset($data['new_status'])) {
    $reservation_id = intval($data['reservation_id']);
    $new_status = $data['new_status'];

    // Statuts autorisés
    $allowed = ['en attente', 'confirmé', 'annulé', 'maintenance'];
    if (!in_array($new_status, $allowed)) {
        http_response_code(400);
        echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
        exit();
    }

    $query = "UPDATE reservations SET statut = :new_status WHERE reservation_id = :reservation_id";
    $stmt = $conn->prepare($query);
    if ($stmt->execute([':new_status' => $new_status, ':reservation_id' => $reservation_id])) {
        echo json_encode(["success" => true, "message" => "Statut mis à jour avec succès"]);
    } else {
        echo json_encode(["success" => false, "message" => "Erreur lors de la mise à jour"]);
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Données invalides"]);
}
exit();
?>