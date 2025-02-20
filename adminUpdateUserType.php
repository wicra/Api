<?php
require 'db.php';

header("Content-Type: application/json");

$data = json_decode(file_get_contents("php://input"), true);
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['target_user_id']) ||
    empty($data['new_type_id']) ||
    empty($data['admin_id']) ||
    empty($data['admin_password'])
) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

$target_user_id = intval($data['target_user_id']);
$new_type_id = intval($data['new_type_id']);
$admin_id = intval($data['admin_id']);
$admin_password = $data['admin_password'];

// Vérifier l'identité de l'administrateur
$stmt = $conn->prepare("SELECT mdp, type_user_id FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Admin non trouvé"]);
    exit();
}

// Vérification du mot de passe admin 
if (hash('sha256', $admin_password) !== $admin['mdp']) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Mot de passe admin incorrect"]);
    exit();
}

// Vérifier que l'admin a bien le type 'Admin'
if ($admin['type_user_id'] != 3) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit();
}

// Types autorisés : 1 (Habitan), 2 (Capitaine), 3 (Admin)
$allowed = [1, 2, 3];
if (!in_array($new_type_id, $allowed)) {
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Type d'utilisateur non autorisé"]);
    exit();
}

try {
    $query = "UPDATE users SET type_user_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$new_type_id, $target_user_id]);
    echo json_encode(["success" => true, "message" => "Type d'utilisateur mis à jour"]);
} catch(Exception $e) {
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>