<?php
require '../db/db.php';

$data = json_decode(file_get_contents("php://input"), true);

if($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    empty($data['name']) || 
    empty($data['email']) || 
    empty($data['password'])) {
    http_response_code(400);
    echo json_encode(["message" => "Tous les champs sont requis"]);
    exit();
}

try {
    // Vérification email unique
    $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $data['email']]);
    
    if($check->rowCount() > 0) {
        http_response_code(409);
        echo json_encode(["message" => "Cet email est déjà utilisé"]);
        exit();
    }

    // Hash du mot de passe
    $hashedPassword = hash('sha256', $data['password']);

    // Insertion utilisateur
    $stmt = $conn->prepare("INSERT INTO users 
        (name, email, mdp, type_user_id, created_at) 
        VALUES (:name, :email, :mdp, 1, NOW())");

    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':mdp' => $hashedPassword
    ]);

    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Utilisateur créé avec succès"]);

} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(["message" => "Erreur d'inscription: " . $e->getMessage()]);
}
?>