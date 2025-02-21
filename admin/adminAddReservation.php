<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['user_id']) || 
    empty($data['pont_id']) || 
    empty($data['date']) || 
    empty($data['start_time']) ||
    empty($data['statut'])
) {
    // ERREUR 400 : MAUVAISE REQUETE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DES DONNEES
$user_id    = intval($data['user_id']);
$pont_id    = intval($data['pont_id']);
$date       = $data['date'];           // Format : 'YYYY-MM-DD'
$start_time = $data['start_time'];       // Format : 'HH:mm'
$adminStatut= $data['statut'];

// CONSTRUCTION DE L'HEURE DE DEBUT ET CALCUL DE L'HEURE DE FIN
$date_debut = $date . " " . $start_time . ":00";  
$startDateTime = new DateTime($date_debut);
$endDateTime = clone $startDateTime;
$endDateTime->add(new DateInterval('PT30M'));
$end_time = $endDateTime->format("H:i");
$date_fin = $date . " " . $end_time . ":00";

// VALIDATION : l'heure de fin doit être postérieure à l'heure de début
if (strtotime($date_fin) <= strtotime($date_debut)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "L'heure de fin doit être postérieure à l'heure de début"]);
    exit();
}

// VERIFICATION DE L'EXISTENCE D'UNE RESERVATION DOUBLON POUR CE USER, CE PONT ET CETTE DATE HEURE
$checkQuery = "SELECT * FROM reservations WHERE user_id = ? AND pont_id = ? AND date_debut = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->execute([$user_id, $pont_id, $date_debut]);
if ($checkStmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "Il a déjà une réservation à cette date et heure"]);
    exit();
}

// VERIFICATION DU STATUT AUTORISE
$allowedStatus = ["en attente", "confirmé", "annulé", "maintenance"];
if (!in_array($adminStatut, $allowedStatus)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

// REQUETE SQL POUR L'AJOUT DE LA RESERVATION
try {
    $query = "INSERT INTO reservations (user_id, pont_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id, $pont_id, $date_debut, $date_fin, $adminStatut]);
    
    // SUCCES 201 : RESSOURCE CREEE
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Réservation ajoutée avec succès"]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur lors de l'ajout de la réservation : " . $e->getMessage()]);
}
exit();
?>