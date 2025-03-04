<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

error_reporting(E_ALL);
ini_set('display_errors', 1);
header("Content-Type: application/json");
require '../db/db.php';

if (!isset($_GET['bateau_id']) || !isset($_GET['user_id'])) {
    http_response_code(400);
    echo json_encode([
        "success" => false,
        "message" => "Les paramètres bateau_id et user_id sont requis"
    ]);
    exit();
}

$bateau_id = intval($_GET['bateau_id']);
$user_id = intval($_GET['user_id']);

$query = "DELETE FROM bateaux WHERE bateau_id = :bateau_id AND user_id = :user_id";
$stmt = $conn->prepare($query);
if (!$stmt->execute([':bateau_id' => $bateau_id, ':user_id' => $user_id])) {
    http_response_code(500);
    echo json_encode([
        "success" => false,
        "message" => "Erreur SQL lors de la suppression",
        "error"   => $stmt->errorInfo()
    ]);
    exit();
}

echo json_encode([
    "success" => true,
    "message" => "Bateau supprimé avec succès"
]);
exit();
?>