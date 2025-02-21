<?php
// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES CHAMPS OBLIGATOIRES
if($_SERVER['REQUEST_METHOD'] !== 'POST' || 
    empty($data['name']) || 
    empty($data['email']) || 
    empty($data['password'])) {
    // ERREUR 400 : TOUS LES CHAMPS SONT REQUIS
    http_response_code(400);
    echo json_encode(["message" => "Tous les champs sont requis"]);
    exit();
}

try {
    // VERIFICATION DE L'UNICITE DE L'EMAIL
    $check = $conn->prepare("SELECT id FROM users WHERE email = :email");
    $check->execute([':email' => $data['email']]);
    
    if($check->rowCount() > 0) {
        // ERREUR 409 : EMAIL DEJA UTILISE
        http_response_code(409);
        echo json_encode(["message" => "Cet email est déjà utilisé"]);
        exit();
    }

    // HASH DU MOT DE PASSE
    $hashedPassword = hash('sha256', $data['password']);

    // INSERTION DE L'UTILISATEUR DANS LA BASE DE DONNEES
    $stmt = $conn->prepare("INSERT INTO users 
        (name, email, mdp, type_user_id, created_at) 
        VALUES (:name, :email, :mdp, 1, NOW())");

    $stmt->execute([
        ':name' => $data['name'],
        ':email' => $data['email'],
        ':mdp' => $hashedPassword
    ]);

    // SUCCÈS 201 : UTILISATEUR CREE
    http_response_code(201);
    echo json_encode(["success" => true, "message" => "Utilisateur créé avec succès"]);

} catch(PDOException $e) {
    // ERREUR 500 : ERREUR SERVEUR LORS DE L'INSCRIPTION
    http_response_code(500);
    echo json_encode(["message" => "Erreur d'inscription: " . $e->getMessage()]);
}
?>