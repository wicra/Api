<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require '../db/db.php';

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "USER_ID REQUIRED"
    ]);
    exit();
}


$user_id = intval($_GET['user_id']);

$query = "SELECT 
    r.reservation_id,
    r.statut,
    DATE(r.date_reservation) AS reservation_date,
    p.nom AS pont_name,
    b.nom AS bateau_name,
    CONCAT(c.libelle, ' (', c.type_creneau, ' - ', c.periode, ' | ', c.heure_debut, ' - ', c.heure_fin, ')') AS creneau
FROM reservations r
JOIN ponts p ON r.pont_id = p.pont_id
JOIN creneaux c ON r.creneau_id = c.creneau_id
JOIN bateaux b ON r.bateau_id = b.bateau_id
WHERE r.user_id = :user_id
ORDER BY r.date_reservation DESC; ";

$stmt = $conn->prepare($query);
if (!$stmt->execute([':user_id' => $user_id])) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "SQL ERROR",
        "error"   => $stmt->errorInfo()
    ]);
    exit();
}

$reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

echo json_encode([
    "success" => true,
    "reservations" => $reservations
]);
exit();
?>