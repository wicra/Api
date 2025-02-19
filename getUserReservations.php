<?php
require 'db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

$user_id = intval($_GET['user_id']);

try {
    $query = "
      SELECT r.reservation_id, r.pont_id, p.nom, r.date_debut, r.date_fin, r.statut
      FROM reservations r
      JOIN ponts p ON r.pont_id = p.pont_id
      WHERE r.user_id = ?
      ORDER BY r.date_debut DESC
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "reservations" => $reservations]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
exit();
?>