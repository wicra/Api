<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES PARAMETRES OBLIGATOIRES
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['reservation_id']) ||
    empty($data['user_id']) ||
    empty($data['statut'])
) {
    // ERREUR 400 : PARAMETRES MANQUANTS
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DES DONNEES
$reservation_id = intval($data['reservation_id']);
$user_id = intval($data['user_id']);
$statut = $data['statut'];

// DEFINITION DES STATUTS AUTORISES
// Pour un utilisateur non admin, seuls "en attente", "annulé", "confirmé" et "maintenance" sont autorisés
$allowed = ["en attente", "annulé", "confirmé", "maintenance"];
if (!in_array($statut, $allowed)) {
    // ERREUR 400 : STATUT NON AUTORISE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

try {
    // REQUETE SQL POUR METTRE A JOUR LE STATUT DE LA RESERVATION POUR UN UTILISATEUR DONNE
    $query = "UPDATE reservations SET statut = ? WHERE reservation_id = ? AND user_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$statut, $reservation_id, $user_id]);
    
    // SUCCES : MISE A JOUR EFFECTUEE
    echo json_encode(["success" => true, "message" => "Mise à jour effectuée"]);
} catch (Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
exit();
?>