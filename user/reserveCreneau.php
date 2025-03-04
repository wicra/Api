<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

require '../db/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(400);
    echo json_encode("METHODE NON AUTORISEE");
    exit();
}

if (empty($data)) {
    http_response_code(400);
    echo json_encode("AUCUNE DONNEE RECUE");
    exit();
}

if (empty($data['user_id']) || empty($data['pont_id']) || empty($data['creneau_id']) || empty($data['reservation_date']) || empty($data['bateau_id'])) {
    http_response_code(400);
    echo json_encode("PARAMETRES MANQUANTS");
    exit();
}

$user_id          = intval($data['user_id']);
$pont_id          = intval($data['pont_id']);
$creneau_id       = intval($data['creneau_id']);
$bateau_id        = intval($data['bateau_id']);
$reservation_date = $data['reservation_date'];  // format attendu : 'YYYY-MM-DD'

// Vérification que le pont existe
$stmt = $conn->prepare("SELECT pont_id, nom FROM ponts WHERE pont_id = ?");
$stmt->execute([$pont_id]);
$pont = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$pont) {
    http_response_code(404);
    echo json_encode("PONT NON TROUVE");
    exit();
}

// Vérification du créneau pour ce pont et récupération de la capacité
$stmt = $conn->prepare("SELECT capacite_max FROM creneaux WHERE creneau_id = ? AND pont_id = ?");
$stmt->execute([$creneau_id, $pont_id]);
$creneau = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$creneau) {
    http_response_code(404);
    echo json_encode("CRENEAU NON TROUVE POUR CE PONT");
    exit();
}
$capacite_max = $creneau['capacite_max'];

// Vérification du nombre de réservations actives (statut CONFIRME ou MAINTENANCE)
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE creneau_id = ? AND DATE(date_reservation) = ? AND statut IN ('CONFIRME', 'MAINTENANCE')");
$stmt->execute([$creneau_id, $reservation_date]);
$countData = $stmt->fetch(PDO::FETCH_ASSOC);
if ($countData['count'] >= $capacite_max) {
    http_response_code(409);
    echo json_encode("CE CRENEAU EST COMPLET");
    exit();
}

// Empêcher qu’un utilisateur réserve plusieurs fois le même créneau à la même date
$stmt = $conn->prepare("SELECT COUNT(*) as count FROM reservations WHERE user_id = ? AND creneau_id = ? AND DATE(date_reservation) = ? AND statut != 'ANNULE'");
$stmt->execute([$user_id, $creneau_id, $reservation_date]);
$userReservation = $stmt->fetch(PDO::FETCH_ASSOC);
if ($userReservation['count'] > 0) {
    http_response_code(409);
    echo json_encode("VOUS AVEZ DEJA RESERVE CE CRENEAU A CETTE DATE");
    exit();
}

// Insertion de la réservation en incluant le bateau_id
try {
    $query = "INSERT INTO reservations (user_id, pont_id, creneau_id, bateau_id, date_reservation) VALUES (?, ?, ?, ?, ?)";
    $stmt  = $conn->prepare($query);
    $stmt->execute([$user_id, $pont_id, $creneau_id, $bateau_id, $reservation_date]);
    http_response_code(201);
    echo json_encode("RESERVATION CREEE AVEC SUCCES");
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode("ERREUR LORS DE L'INSERTION");
}
exit();
?>