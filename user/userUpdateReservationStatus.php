<?php
// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// LECTURE DES DONNEES JSON ENVOYEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES PARAMETRES OBLIGATOIRES
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

// RECUPERATION DES DONNEES
$reservation_id = intval($data['reservation_id']);
$user_id = intval($data['user_id']);
$statut = $data['statut'];

// POUR UN UTILISATEUR NON ADMIN, SEULEMENT "en attente" ET "annulé" SONT AUTORISÉS
$allowed = ["en attente", "annulé"];
if (!in_array($statut, $allowed)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Statut non autorisé"]);
    exit();
}

try {
    // REQUETE SQL POUR METTRE A JOUR LE STATUT DE LA RESERVATION POUR L'UTILISATEUR DONNE
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