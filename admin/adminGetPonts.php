<?php
// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

try {
    // REQUETE SQL POUR RECUPERER TOUS LES PONTS ORDONNÉS PAR PONT_ID
    $query = "SELECT * FROM ponts ORDER BY pont_id ASC";
    $stmt = $conn->query($query);
    $ponts = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // SUCCES : RENVOIE LA LISTE DES PONTS TROUVÉS
    echo json_encode(["success" => true, "ponts" => $ponts]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>