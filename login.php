<?php
require 'db.php';

$data = json_decode(file_get_contents("php://input"), true);

if($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['email']) || empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["message" => "Données invalides"]);
    exit();
}

try {
    // Récupération utilisateur
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$user || hash('sha256', $data['password']) !== $user['mdp']) {
        http_response_code(401);
        echo json_encode(["message" => "Email ou mot de passe incorrect"]);
        exit();
    }

    // Mise à jour dernière connexion
    $update = $conn->prepare("UPDATE users SET last_sign = NOW() WHERE id = :id");
    $update->execute([':id' => $user['id']]);

    // Réponse réussie
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user['id'],
            "name" => $user['name'],
            "email" => $user['email'],
            "is_admin" => $user['is_admin']
        ]
    ]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur serveur: " . $e->getMessage()]);
}
?>