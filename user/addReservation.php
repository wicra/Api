<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// LECTURE DES DONNEES JSON ENVOYEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES PARAMETRES OBLIGATOIRES
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['user_id']) || 
    empty($data['pont_id']) || 
    empty($data['date']) || 
    empty($data['start_time'])
) {
    // ERREUR 400 : PARAMETRES MANQUANTS
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DES DONNEES
$user_id = intval($data['user_id']);
$pont_id = intval($data['pont_id']);
$date = $data['date'];           // Format : 'YYYY-MM-DD'
$start_time = $data['start_time']; // Format : 'HH:mm'

// CONSTRUCTION DE L'HEURE COMPLETE DE DEBUT
$date_debut = $date . " " . $start_time . ":00";

// CALCUL AUTOMATIQUE DE L'HEURE DE FIN (30 MINUTES APRES)
$startDateTime = new DateTime($date_debut);
$endDateTime = clone $startDateTime;
$endDateTime->add(new DateInterval('PT30M'));
$end_time = $endDateTime->format("H:i");
$date_fin = $date . " " . $end_time . ":00";

// VERIFICATION QUE LA DATE DE FIN EST POSTERIEURE A LA DATE DE DEBUT
if (strtotime($date_fin) <= strtotime($date_debut)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "L'heure de fin doit être postérieure à l'heure de début"]);
    exit();
}

// VERIFICATION DE L'EXISTANCE D'UNE RESERVATION DOUBLON POUR CE USER, CE PONT, A CETTE DATE ET HEURE DE DEBUT
$checkQuery = "SELECT * FROM reservations WHERE user_id = ? AND pont_id = ? AND date_debut = ?";
$checkStmt = $conn->prepare($checkQuery);
$checkStmt->execute([$user_id, $pont_id, $date_debut]);
if ($checkStmt->rowCount() > 0) {
    http_response_code(409);
    echo json_encode(["success" => false, "message" => "Il a déjà une réservation à cette date et heure"]);
    exit();
}

// DEFINITION DU STATUT PAR DEFAUT : 'en attente'
$statut = "en attente";

try {
    // REQUETE SQL POUR INSERER LA NOUVELLE RESERVATION DANS LA BASE DE DONNEES
    $query = "INSERT INTO reservations (user_id, pont_id, date_debut, date_fin, statut) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$user_id, $pont_id, $date_debut, $date_fin, $statut]);

    // SUCCES 201 : RESERVATION AJOUTEE
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Réservation ajoutée avec succès"]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR LORS DE L'AJOUT DE LA RESERVATION
    http_response_code(500);
    echo json_encode([
        "success" => false, 
        "message" => "Erreur lors de l'ajout de la réservation : " . $e->getMessage()
    ]);
}
exit();
?>