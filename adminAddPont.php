<?php
require 'db.php';
header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['nom']) || empty($data['adresse'])) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Les paramètres 'nom' et 'adresse' sont obligatoires"]);
    exit();
}

$nom = $data['nom'];
$adresse = $data['adresse'];

try {
    $query = "INSERT INTO ponts (nom, adresse) VALUES (?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->execute([$nom, $adresse]);
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Pont ajouté avec succès"]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>