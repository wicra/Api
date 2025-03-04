<?php
// REDIRECTION DE SECURITE
header("Location: ../index.php");
exit();

// DEFINIR LES HEADERS POUR L'ACCES CORS ET LE FORMAT JSON
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

// DEFINITION DES PARAMETRES DE CONNEXION A LA BASE DE DONNEES
$servername = "127.0.0.1";
$port = "8889"; 
$username = "flutter";
$password = "flutter";
$dbname = "flutter_db";

try {
    // ETABLISSEMENT DE LA CONNEXION AVEC PDO
    $conn = new PDO("mysql:host=$servername;port=$port;dbname=$dbname;charset=utf8", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    // ERREUR 500 : ECHEC DE CONNEXION A LA BASE DE DONNEES
    http_response_code(500);
    echo json_encode(["message" => "Connection failed: " . $e->getMessage()]);
    exit();
}
?>