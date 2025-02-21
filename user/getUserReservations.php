<?php
// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DU PARAMETRE 'user_id'
if ($_SERVER['REQUEST_METHOD'] !== 'GET' || empty($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DU PARAMETRE 'user_id'
$user_id = intval($_GET['user_id']);

try {
    // REQUETE SQL POUR RECUPERER LES RESERVATIONS DE L'UTILISATEUR
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
    
    // SUCCÈS : RENVOIE LA LISTE DES RESERVATIONS TROUVÉES
    echo json_encode(["success" => true, "reservations" => $reservations]);
} catch (Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
exit();
?>