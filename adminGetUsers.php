<?php
require 'db.php';

header("Content-Type: application/json");

$admin_id = isset($_GET['admin_id']) ? intval($_GET['admin_id']) : 0;
if ($admin_id <= 0) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètre admin_id manquant"]);
    exit();
}

try {
    $query = "SELECT u.id, u.name, u.email, u.type_user_id, t.name AS user_type_name 
              FROM users u
              JOIN type_user t ON u.type_user_id = t.id
              WHERE u.id != ? AND u.type_user_id != 3";
    $stmt = $conn->prepare($query);
    $stmt->execute([$admin_id]);
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode(["success" => true, "users" => $users]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>