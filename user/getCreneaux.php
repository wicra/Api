<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

require '../db/db.php';

// Vérification des paramètres : pont_id
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['pont_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètre pont_id manquant"]);
    exit();
}

$pont_id = intval($_GET['pont_id']);

// Définir la période en fonction du mois actuel
$month = date('n'); // 1 à 12
if ($month >= 4 && $month <= 10) {
    $periode = 'été';
} else {
    $periode = 'hiver';
}

try {
    // Récupérer les créneaux pour le pont donné et la période correspondante
    $query = "SELECT creneau_id, type_creneau, heure_debut, heure_fin, capacite_max 
              FROM creneaux 
              WHERE pont_id = ? AND periode = ? 
              ORDER BY heure_debut ASC";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id, $periode]);
    $creneaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode(["success" => true, "creneaux" => $creneaux]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>