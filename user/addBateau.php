<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();


error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require '../db/db.php';

$data = json_decode(file_get_contents('php://input'), true);

if (
    !isset($data['user_id']) || 
    !isset($data['nom']) || 
    !isset($data['immatriculation']) || 
    !isset($data['hauteur_mat']) ||
    trim($data['nom']) === '' ||
    trim($data['immatriculation']) === '' ||
    trim($data['hauteur_mat']) === ''
) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Les paramètres user_id, nom, immatriculation et hauteur_mat sont requis et ne doivent pas être vides"
    ]);
    exit();
}

$user_id = intval($data['user_id']);
$nom = trim($data['nom']);
$immatriculation = trim($data['immatriculation']);

// Remplacer la virgule par un point pour s'assurer que la conversion se fasse correctement
$hauteur_mat_input = str_replace(',', '.', trim($data['hauteur_mat']));
$hauteur_mat = floatval($hauteur_mat_input);

// Optionnel : vous pouvez vérifier que la valeur est > 0
if ($hauteur_mat <= 0) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "La hauteur doit être un nombre positif"
    ]);
    exit();
}

$query = "INSERT INTO bateaux (nom, immatriculation, hauteur_mat, user_id) VALUES (:nom, :immatriculation, :hauteur_mat, :user_id)";
$stmt = $conn->prepare($query);
if (!$stmt->execute([
    ':nom' => $nom, 
    ':immatriculation' => $immatriculation, 
    ':hauteur_mat' => $hauteur_mat, 
    ':user_id' => $user_id
])) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur SQL",
        "error"   => $stmt->errorInfo()
    ]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Bateau ajouté avec succès"
]);
exit();
?>