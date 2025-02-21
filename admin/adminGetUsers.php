<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

// RECUPERATION DU PARAMETRE 'admin_id' VIA LA REQUETE GET
$admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : 0;
if ($admin_id <= 0) {
    // ERREUR 400 : PARAMETRE 'admin_id' MANQUANT
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètre admin_id manquant"]);
    exit();
}

try {
    // REQUETE SQL POUR RECUPERER LES UTILISATEURS EN EXCLUANT L'ADMIN ET LES UTILISATEURS DE TYPE 3
    $query = "SELECT u.id, u.name, u.email, u.type_user_id, t.name AS user_type_name 
              FROM users u
              JOIN type_user t ON u.type_user_id = t.id
              WHERE u.id != ? AND u.type_user_id != 3";
    $stmt = $conn->prepare($query);
    $stmt->execute([$admin_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // SUCCES : RENVOIE LA LISTE DES UTILISATEURS TROUVÉS
    echo json_encode(["success" => true, "users" => $users]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>