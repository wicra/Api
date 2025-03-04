<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

try {
    // REQUETE SQL POUR RECUPERER LES PONTS AVEC LEURS IDENTIFIANTS ET NOMS
    $stmt = $conn->query("SELECT pont_id, nom FROM ponts");
    $ponts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // SUCCÈS : RENVOIE LA LISTE DES PONTS TROUVÉS
    echo json_encode(["success" => true, "ponts" => $ponts]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>