<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// EN-TETE DE LA REQUETE    
header("Content-Type: application/json");

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['nom']) || empty($data['adresse'])) {
    
    // ERREUR 400 : MAUVAISE REQUETE
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Les paramètres 'nom' et 'adresse' sont obligatoires"]);
    exit();
}

// RECUPERATION DES DONNEES
$nom = $data['nom'];
$adresse = $data['adresse'];

// REQUETE SQL
try {
    $query = "INSERT INTO ponts (nom, adresse) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nom, $adresse]);

    // SUCCES 201 : RESSOURCE CREEE
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Pont ajouté avec succès"]);
} 

// ERREUR 500 : ERREUR SERVEUR
catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>