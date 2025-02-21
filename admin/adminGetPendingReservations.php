<?php
// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

// CALCUL DE LA DATE ACTUELLE ET DE LA DATE DANS 1 MOIS
$today = date('Y-m-d H:i:s');
$oneMonthLater = date('Y-m-d H:i:s', strtotime('+1 month'));

try {
    // REQUETE SQL POUR RECUPERER LES RESERVATIONS EN ATTENTE DANS L'INTERVALLE
    $query = "
      SELECT 
        r.reservation_id, 
        r.pont_id, 
        p.nom AS pont_name,
        r.date_debut, 
        r.date_fin, 
        r.statut, 
        u.name AS user_name, 
        u.email AS user_email
      FROM reservations r
      JOIN users u ON r.user_id = u.id
      JOIN ponts p ON r.pont_id = p.pont_id
      WHERE r.statut = 'en attente'
        AND r.date_debut BETWEEN ? AND ?
      ORDER BY r.date_debut ASC
    ";
    $stmt = $conn->prepare($query);
    $stmt->execute([$today, $oneMonthLater]);
    $reservations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // SUCCES : RENVOIE DES RESERVATIONS TROUVÉES
    echo json_encode(["success" => true, "reservations" => $reservations]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>