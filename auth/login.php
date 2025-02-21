<?php

// CONNEXION A LA BASE DE DONNEES
require '../db/db.php';

// RECUPERATION DES DONNEES POSTEES PAR LE CLIENT
$data = json_decode(file_get_contents("php://input"), true);

// VERIFICATION DE LA VALIDITE DE LA REQUETE ET DES PARAMETRES OBLIGATOIRES
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || empty($data['email']) || empty($data['password'])) {
    // ERREUR 400 : DONNEES INVALIDES
    http_response_code(400);
    echo json_encode(["message" => "Données invalides"]);
    exit();
}

try {
    // Récupération de l'utilisateur en fonction de l'email
    $stmt = $conn->prepare("SELECT * FROM users WHERE email = :email");
    $stmt->execute([':email' => $data['email']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // VERIFICATION DU MOT DE PASSE DE L'UTILISATEUR
    if (!$user || hash('sha256', $data['password']) !== $user['mdp']) {
        // ERREUR 401 : EMAIL OU MOT DE PASSE INCORRECT
        http_response_code(401);
        echo json_encode(["message" => "Email ou mot de passe incorrect"]);
        exit();
    }

    // MISE A JOUR DE LA DERNIERE CONNEXION DE L'UTILISATEUR
    $update = $conn->prepare("UPDATE users SET last_sign = NOW() WHERE id = :id");
    $update->execute([':id' => $user['id']]);

    // SUCCES : AUTHENTIFICATION REUSSIE ET ENVOI DES DONNEES DE L'UTILISATEUR
    http_response_code(200);
    echo json_encode([
        "success" => true,
        "user" => [
            "id" => $user['id'],
            "name" => $user['name'],
            "email" => $user['email'],
            "type_user_id" => $user['type_user_id']
        ]
    ]);

} catch(PDOException $e) {
    // ERREUR 500 : ERREUR SERVEUR
    http_response_code(500);
    echo json_encode(["message" => "Erreur serveur: " . $e->getMessage()]);
}
?>