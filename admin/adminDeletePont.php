<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TÊTE DE LA REQUETE    
header("Content-Type: application/json");

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['pont_id'])) {
    // ERREUR 400 : MAUVAISE REQUETE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètre 'pont_id' manquant"]);
    exit();
}

// RECUPERATION DU PARAMETRE 'pont_id'
$pont_id = intval($data['pont_id']);

try {
    // REQUETE SQL POUR SUPPRIMER LE PONT
    $query = "DELETE FROM ponts WHERE pont_id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$pont_id]);

    // SUCCES : PONT SUPPRIME
    echo json_encode(["success" => true, "message" => "Pont supprimé avec succès"]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>