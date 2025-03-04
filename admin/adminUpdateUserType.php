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
if (
    $_SERVER['REQUEST_METHOD'] !== 'POST' ||
    empty($data['target_user_id']) ||
    empty($data['new_type_id']) ||
    empty($data['admin_id']) ||
    empty($data['admin_password'])
) {
    // ERREUR 400 : PARAMÈTRES MANQUANTS
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Paramètres manquants"]);
    exit();
}

// RECUPERATION DES DONNEES
$target_user_id = intval($data['target_user_id']);
$new_type_id = intval($data['new_type_id']);
$admin_id = intval($data['admin_id']);
$admin_password = $data['admin_password'];

// VERIFICATION DE L'IDENTITE DE L'ADMINISTRATEUR
$stmt = $conn->prepare("SELECT mdp, type_user_id FROM users WHERE id = ?");
$stmt->execute([$admin_id]);
$admin = $stmt->fetch(PDO::FETCH_ASSOC);
if (!$admin) {
    // ERREUR 401 : ADMIN NON TROUVÉ
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Admin non trouvé"]);
    exit();
}

// VERIFICATION DU MOT DE PASSE ADMIN
if (hash('sha256', $admin_password) !== $admin['mdp']) {
    // ERREUR 403 : MOT DE PASSE ADMIN INCORRECT
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Mot de passe admin incorrect"]);
    exit();
}

// VERIFICATION DU TYPE DE L'ADMINISTRATEUR
if ($admin['type_user_id'] != 3) {
    // ERREUR 403 : ACCÈS REFUSÉ
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "Accès refusé"]);
    exit();
}

// TYPES UTILISATEURS AUTORISÉS : 1 (Habitant), 2 (Capitaine), 3 (Admin)
$allowed = [1, 2, 3];
if (!in_array($new_type_id, $allowed)) {
    // ERREUR 400 : TYPE D'UTILISATEUR NON AUTORISÉ
    http_response_code(400);
    echo json_encode(["success" => false, "message" => "Type d'utilisateur non autorisé"]);
    exit();
}

try {
    // REQUETE SQL POUR METTRE A JOUR LE TYPE D'UTILISATEUR
    $query = "UPDATE users SET type_user_id = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->execute([$new_type_id, $target_user_id]);
    
    // SUCCÈS : TYPE D'UTILISATEUR MIS À JOUR
    echo json_encode(["success" => true, "message" => "Type d'utilisateur mis à jour"]);
} catch(Exception $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["success" => false, "message" => "Erreur: " . $e->getMessage()]);
}
exit();
?>