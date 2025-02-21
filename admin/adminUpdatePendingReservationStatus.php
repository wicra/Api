<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['reservation_id']) ||
    empty($data['statut'])
) {
    // ERREUR 400 : MAUVAISE REQUETE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DES DONNEES
$reservation_id = intval($data['reservation_id']);
$statut = $data['statut'];

// DEFINITION DES STATUTS AUTORISES
$allowed = ["confirmé", "annulé", "en attente", "maintenance"];
if (!in_array($statut, $allowed)) {
    // ERREUR 400 : STATUT NON AUTORISE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

try {
    // REQUETE SQL POUR METTRE A JOUR LE STATUT DE LA RESERVATION
    $query = "UPDATE reservations SET statut = ? WHERE reservation_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$statut, $reservation_id]);
    
    // SUCCES : STATUT MIS A JOUR
    echo json_encode(["success" => true, "message" => "Statut mis à jour"]);
} catch (Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>