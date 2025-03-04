<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require '../db/db.php';

if (!isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "USER_ID REQUIRED"
    ]);
    exit();
}

$user_id = intval($_GET['user_id']);
$query = "SELECT bateau_id, nom, immatriculation, hauteur_mat, created_at FROM bateaux WHERE user_id = :user_id ORDER BY created_at DESC";
$stmt = $conn->prepare($query);
if (!$stmt->execute([':user_id' => $user_id])) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur SQL",
        "error"   => $stmt->errorInfo()
    ]);
    exit();
}

$bateaux = $stmt->fetchAll(PDO::FETCH_ASSOC);
echo json_encode([
    "success" => true,
    "bateaux"  => $bateaux
]);
exit();
?>